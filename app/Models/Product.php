<?php

namespace App\Models;

use Framework\Core\Model;


class Product extends Model
{

    protected $table = 'products';

    public string $name;
    public string $description;
    public float $price;
    public string $created_at;
    public string $updated_at;
}