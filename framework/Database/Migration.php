<?php

namespace Framework\Database;

use PDO;

abstract class Migration
{
    abstract public function up(PDO $pdo);
    abstract public function down(PDO $pdo);
}