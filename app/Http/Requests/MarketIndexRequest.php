<?php

namespace App\Http\Requests;

class MarketIndexRequest extends JsonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            "name" => "string",
            "issuer" => "string",
            "frequency" => "string",
            "vessel_type_id" => "integer|exists:vessel_types,id",
            "vessel_size" => "string",
            "source" => "string"
        ];

        return $this->applyRequirements($rules);
    }

    /**
     * Handle field requirements for store and edit
     *
     * @param array $rules
     *
     * @return array
     */
    protected function applyRequirements(array $rules) {
        if ($this->getMethod() === 'PUT') {
            return array_map(function($rule) {
                return "nullable|" . $rule;
            }, $rules);
        }

        return array_map(function($rule) {
            return "required|".$rule;
        }, $rules);
    }
}
