<?php

namespace App\Models;

use Framework\Core\Model;


class User extends Model
{

    protected $table = 'users';

    public string $name;
    public string $email;
    public string $password;
    public string $role;
    public string $created_at;
    public string $updated_at;
}