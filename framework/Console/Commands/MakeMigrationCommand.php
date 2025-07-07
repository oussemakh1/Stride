<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

class MakeMigrationCommand extends Command
{
    protected $name = 'make:migration';
    protected $description = 'Creates a new migration file.';

    public function execute(array $args)
    {
        if (empty($args[0])) {
            echo "Usage: php stride make:migration <name>\n";
            return;
        }

        $name = $args[0];
        $timestamp = date('YmdHis');
        $fileName = $timestamp . '_' . $name . '.php';
        $className = ucfirst(preg_replace_callback('/_([a-z])/', function ($matches) { return strtoupper($matches[1]); }, $name));

        $stub = <<<EOT
<?php

use Framework\Database\Migration;
use PDO;

class {$className} extends Migration
{
    public function up(PDO \$pdo)
    {
        // Implement your migration logic here to create or alter tables.
        // Example: \$pdo->exec("CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255))");
    }

    public function down(PDO \$pdo)
    {
        // Implement your migration rollback logic here to drop or revert tables.
        // Example: \$pdo->exec("DROP TABLE users");
    }
}
EOT;

        $filePath = __DIR__ . '/../../../migrations/' . $fileName;

        if (!file_put_contents($filePath, $stub)) {
            echo "Error: Could not write migration file to {$filePath}\n";
            return;
        }

        echo "Migration created: {$fileName}\n";
    }
}
