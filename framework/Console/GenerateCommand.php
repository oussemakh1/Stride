<?php
namespace Pc\Oop\commands;

use PDO;
use PDOException;


if (empty($argv[1]) || empty($argv[2])) {
    echo "Usage: php generate.php <filename> <tablename>\n";
    exit(1);
}

$envFile = file_get_contents('../.env');
// Parse the .env file line by line
$lines = explode(";", $envFile);
foreach ($lines as $line) {
    // Skip empty lines and lines starting with #
    if (empty($line) || strpos($line, '#') === 0) {
        continue;
    }

    // Extract key and value pairs
    list($key, $value) = explode(':', $line, 2);
    $key = trim($key);
    $value = trim($value);
    // Set the environment variable
    putenv("$key=$value");
}

function redirect($url) {
    header("Location: $url");
}class Generate {

    public function __construct($argv)
    {
        $filename = strtolower($argv[1]);
        $controller = $argv[1] . "Controller.php";
        $tableName = $argv[2];
        $model = $argv[1] . ".php";


        // Database configuration
        if (!file_exists("../.env")) {
            echo ".env file not found.\n";
            exit(1);
        }



        // Connect to the database
        try {
            $dsn ="mysql:host=".getenv('host').";dbname=".getenv("dbname")."";
            $username= getenv('username');
            $password = getenv('password');
            // Create a new PDO instance

            $pdo = new PDO($dsn, $username, $password);    
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Retrieve column names and types
            $stmt = $pdo->query("SHOW COLUMNS FROM $tableName");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Define the PHP code for controller
            $codeController = "<?php

        namespace Pc\\Oop\\controllers;
        use Pc\\Oop\\controllers\\controller;
        use Pc\\Oop\\helpers\\Request;

        class {$argv[1]}Controller extends controller {
            
            public function index() {
                \$this->renderIndex();
            }
            
            public function form() {
                \$this->renderDefaultForm();
            }
            
            public function create(Request \$data) {
                \$this->entity->createRaw(\$data->all());
                return Success('/$filename/index', \"create\");
            }
            
            public function show(int \$id) {
                \$this->renderDefaultEditForm(\$id);
            }
            
            public function update(int \$id, Request \$data) {
                \$this->entity->updateRaw(\$data->all(), \$id);
                return Success('/$filename/index', \"update\");
            }
            
            public function delete(int \$id) {
                \$this->entity->delete(\$id);
                return Success('/$filename/index', \"delete\");
            }
        }";
        if($argv[3] == "auth") {
            $codeController = "<?php

            namespace Pc\\Oop\\controllers;
            use Pc\\Oop\\controllers\\controller;
            use Pc\\Oop\\helpers\\Request;
    
            class {$argv[1]}Controller extends controller {
                
                public function loginForm() {
    
                    \$this->renderCustomForm([\"fields\" => [ [\"name\" =>\"email\", \"type\"=>\"string\"],[\"name\" => \"password\", \"type\" => \"string\"]], \"path\" => \"$filename/login\"]);
                }
    
                public function login(Request \$request) 
                {
    
                    \$user = \$this->entity->find('*','email',\$request->get('email'),false);
    
                    if(password_verify(\$request->get('password'), \$user[0]['password'])) {
                        \$_SESSION[\"user\"] = \$user[0];
                        \$_SESSION[\"userType\"] = \"$filename\";
                    return redirect('/$filename/index');
                    } else {
                        \$_SESSION[\"error\"] = \"password or email is incorrect\";
                        return  redirect(\"/$filename/loginForm\");
                    }
    
                }
                public function index() {
                    \$this->renderIndex();
                }
                
                public function form() {
                    \$this->renderDefaultForm();
                }
                
                public function create(Request \$data) {
                    \$data->set(\"password\",password_hash(\$data->get(\"password\"),PASSWORD_BCRYPT));
                    \$this->entity->createRaw(\$data->all());
                    return Success('/$filename/index', \"create\");
                }
                
                public function show(int \$id) {
                    \$this->renderDefaultEditForm(\$id);
                }
                
                public function update(int \$id, Request \$data) {
                    \$entity = \$this->entity->read(\$id);
        
                    if(\$entity[0][\"password\"] !== \$data->get(\"password\")) {
                        \$data->set(\"password\",password_hash(\$data->get(\"password\"),PASSWORD_BCRYPT));
                    }
            
                    \$this->entity->updateRaw(\$data->all(), \$id);
                    return Success('/$filename/index', \"update\");
                }
                
                public function delete(int \$id) {
                    \$this->entity->delete(\$id);
                    return Success('/$filename/index', \"delete\");
                }
            }";
        }

            // Define the PHP code for model
            $codeModel = "<?php 
        namespace Pc\\Oop\\models;
        use Exception;
        use Pc\\Oop\\database\\connect;
        use Pc\\Oop\\models\\model;

        class {$argv[1]} extends model {";

            // Add public properties based on columns
            foreach ($columns as $column) {
                if($column['Field'] !== "id"):
                $column_name = $column['Field'];
                $column_type = $column['Type'];
               
                if (strpos($column_type, "int") === 0)  {
                    $column_type = "int";
                } 
                
                else if (strpos($column_type, "date") === 0)  {
                    $column_type = "DateTime";
                }
                else if (strpos($column_type, "decimal") === 0 ||strpos($column_type, "float") ) {
                    $column_type = "float";
                } else {
                    $column_type = "string";
                }
                $codeModel .= "\n            public $column_type \$$column_name;";
                endif;
            }

            // Complete the rest of the model code
            $codeModel .= "
            
            public function __construct() {
                \$this->tableName = \"$filename\";
                \$this->connect = new connect();
            }
        }";

            // Write the generated PHP code to files
            file_put_contents("../controllers/$controller", $codeController);
            file_put_contents("../models/$model", $codeModel);

            echo "Files '$filename' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error connecting to the database: " . $e->getMessage() . "\n";
        }
            }
}


new Generate($argv)
?>
