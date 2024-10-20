<?php

namespace Http\Requests\API;

use Http\Requests\FormRequest;
use Http\Response;

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
