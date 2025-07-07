<?php

use Framework\Database\Migration;
use PDO;

class CreateProductsTable extends Migration
{
    public function up(PDO $pdo)
    {
        $pdo->exec("CREATE TABLE products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
    }

    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE products");
    }
}