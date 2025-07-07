<?php

namespace App\Models;

use Framework\Core\Model;


class Post extends Model
{

    protected $table = 'posts';

    public string $content;
    public int $user_id;
}