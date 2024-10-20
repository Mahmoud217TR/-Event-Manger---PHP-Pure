<?php

use Http\Request;
use Http\Response;

if (!function_exists('response')) {
    /**
     * returns an instance of response
     * 
     * @return Response
     */
    function response(): Response
    {
        return Response::make();
    }
}

if (!function_exists('request')) {
    /**
     * returns an instance of request
     *
     * @return Request
     */
    function request(): Request
    {
        return Request::make();
    }
}

if (!function_exists('pdo')) {
    /**
     * returns pdo
     *
     * @return PDO
     */
    function pdo(): PDO
    {
        global $pdo;
        return $pdo;
    }
}