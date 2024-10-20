<?php

namespace App\Http\Requests\API;

use App\Http\Requests\FormRequest;
use App\Http\Response;

abstract class APIFormRequest extends FormRequest
{
    /**
     * Return the validation response
     *
     * @return void
     */
    public static function respondWithErrors(array $errors): void
    {
        Response::make()
            ->code(422)
            ->json([
                'message' => "Invalid data",
                'errors' => $errors
            ]);
    }
}
