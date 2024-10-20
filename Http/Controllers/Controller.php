<?php

namespace Http\Controllers;

abstract class Controller
{
    protected $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }
}
