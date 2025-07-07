<?php

use Framework\Database\Migration;


class CreateTableUsers extends Migration
{
    public function up(PDO $pdo)
    {
        // Implement your migration logic here to create or alter tables.
      $pdo->exec("CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255),email VARCHAR(255) UNIQUE, password VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)");
    }

    public function down(PDO $pdo)
    {
        // Implement your migration rollback logic here to drop or revert tables.
        $pdo->exec("DROP TABLE users");
    }
}