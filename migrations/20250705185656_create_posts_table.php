<?php

use Framework\Database\Migration;
use PDO;

class CreatePostsTable extends Migration
{
    public function up(PDO $pdo)
    {
        // Implement your migration logic here to create or alter tables.
        $pdo->exec("
        CREATE TABLE posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            content TEXT,
            user_id INT,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
        }

    public function down(PDO $pdo)
    {
        // Implement your migration rollback logic here to drop or revert tables.
         $pdo->exec("DROP TABLE posts");
    }
}