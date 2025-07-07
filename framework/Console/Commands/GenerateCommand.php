<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;
use Framework\Database\Connection;
use PDO;
use PDOException;

class GenerateCommand extends Command
{
    protected $name = 'make:model';
    protected $description = 'Generates a new Model and Controller based on a database table.';

    public function execute(array $args)
    {
        if (count($args) < 2) {
            echo "Usage: php stride make:model <ModelName> <tableName> [--soft-deletes]\n";
            return;
        }

        $modelName = ucfirst($args[0]);
        $tableName = $args[1];
        $controllerName = $modelName . 'Controller';
        $useSoftDeletes = in_array('--soft-deletes', $args);
        $isApiController = in_array('--api', $args);

        try {
            $pdo = Connection::getInstance();

            // Retrieve column names and types
            $stmt = $pdo->query("SHOW COLUMNS FROM {$tableName}");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $modelProperties = [];
            $modelUses = [];
            $modelTraits = [];

            foreach ($columns as $column) {
                $columnName = $column['Field'];
                $columnType = $column['Type'];
                $phpType = $this->mapDatabaseTypeToPhpType($columnType);

                if ($columnName !== "id") {
                    $modelProperties[] = "    public {$phpType} \${$columnName};";
                }
            }
            // Add soft deletes if requested
            if ($useSoftDeletes) {
                $modelUses[] = "use Framework\\Core\\SoftDeletes;";
                $modelTraits[] = "    use SoftDeletes;";
                $modelProperties[] = "    public ?string \$deleted_at = null;";
            }

            $modelPropertiesString = implode("\n", $modelProperties);
            $modelUsesString = implode("\n", $modelUses);
            $modelTraitsString = implode("\n", $modelTraits);

            // Generate Model file content
            $modelContent = <<<EOT
<?php

namespace App\Models;

use Framework\\Core\\Model;
{$modelUsesString}

class {$modelName} extends Model
{
{$modelTraitsString}
    protected \$table = '{$tableName}';

{$modelPropertiesString}
}
EOT;

            // Determine controller type
            if ($isApiController) {
                $controllerType = 'api';
            } else {
                echo "Do you want to generate an API controller or a view controller? (api/view): ";
                $controllerType = trim(fgets(STDIN));
            }

            if (strtolower($controllerType) === 'api') {
                $controllerContent = <<<EOT
<?php

namespace App\Controllers;

use Framework\Core\ApiController;
use App\Models\{$modelName};
use Framework\Http\Request;

class {$controllerName} extends ApiController
{
    public function index()
    {
        \$items = {$modelName}::all();
        \$this->json(\$items);
    }

    public function store(Request \$request)
    {
        \$newItem = {$modelName}::create(\$request->all());
        if (\$newItem) {
            \$this->json(\$newItem, 201);
        } else {
            \$this->json(['message' => 'Failed to create item'], 500);
        }
    }

    public function show(int \$id)
    {
        \$item = {$modelName}::find(\$id);
        if (!\$item) {
            \$this->json(['message' => 'Not Found'], 404);
        }
        \$this->json(\$item);
    }

    public function update(int \$id, Request \$request)
    {
        \$updated = {$modelName}::update(\$id, \$request->all());
        if (!\$updated) {
            \$this->json(['message' => 'Not Found or No Changes'], 404);
        }
        \$this->json(['message' => 'Updated successfully']);
    }

    public function destroy(int \$id)
    {
        \$deleted = {$modelName}::delete(\$id);
        if (!\$deleted) {
            \$this->json(['message' => 'Not Found'], 404);
        }
        \$this->json(['message' => 'Deleted successfully']);
    }
}
EOT;
            } else {
                $controllerContent = <<<EOT
<?php

namespace App\Controllers;

use Framework\Core\Controller;
use App\Models\{$modelName};
use Framework\Http\Request;

class {$controllerName} extends Controller
{
    public function index()
    {
        \$items = {$modelName}::all();
        \$this->view('{$tableName}.index', ['items' => \$items]);
    }

    public function create()
    {
        \$this->view('{$tableName}.add');
    }

    public function store(Request \$request)
    {
        if ({$modelName}::create(\$request->all())) {
            header('Location: /{$tableName}');
        } else {
            // Handle error, maybe redirect back with an error message
            header('Location: /{$tableName}/create');
        }
    }

    public function show(int \$id)
    {
        \$item = {$modelName}::find(\$id);
        \$this->view('{$tableName}.show', ['item' => \$item]);
    }

    public function edit(int \$id)
    {
        \$item = {$modelName}::find(\$id);
        \$this->view('{$tableName}.edit', ['item' => \$item]);
    }

    public function update(int \$id, Request \$request)
    {
        if ({$modelName}::update(\$id, \$request->all())) {
            header('Location: /{$tableName}');
        } else {
            // Handle error, maybe redirect back with an error message
            header('Location: /{$tableName}/edit/' . \$id);
        }
    }

    public function destroy(int \$id)
    {
        if ({$modelName}::delete(\$id)) {
            header('Location: /{$tableName}');
        } else {
            // Handle error, maybe redirect back with an error message
            header('Location: /{$tableName}');
        }
    }
}
EOT;
            }

            // Write the generated PHP code to files
            file_put_contents(__DIR__ . "/../../../app/Models/{$modelName}.php", $modelContent);
            file_put_contents(__DIR__ . "/../../../app/Controllers/{$controllerName}.php", $controllerContent);

            echo "Model '{$modelName}.php' and Controller '{$controllerName}.php' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error connecting to the database or table not found: " . $e->getMessage() . "\n";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    private function mapDatabaseTypeToPhpType(string $dbType): string
    {
        if (strpos($dbType, "int") === 0) {
            return "int";
        } elseif (strpos($dbType, "date") === 0 || strpos($dbType, "datetime") === 0 || strpos($dbType, "timestamp") === 0) {
            return "string"; // Or \DateTimeInterface if you want to enforce objects
        } elseif (strpos($dbType, "decimal") === 0 || strpos($dbType, "float") === 0 || strpos($dbType, "double") === 0) {
            return "float";
        } else {
            return "string";
        }
    }
}