<?php
/**
 * Created by PhpStorm.
 * User: monospace
 * Date: 10/12/2018
 * Time: 4:22 μμ
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class JsonRequest extends FormRequest {

    /**
     * Override form request validation failure behaviour
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        throw new HttpResponseException(response()->json(['errors' => $errors
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}