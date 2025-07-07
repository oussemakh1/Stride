<?php

namespace Framework\Console;

abstract class Command
{
    protected $name;
    protected $description;

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    abstract public function execute(array $args);
}
