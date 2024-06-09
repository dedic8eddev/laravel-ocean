<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CargoRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'string',
            'stowage_factor_bale' => 'numeric',
            'stowage_factor_grain' => 'numeric',
            'stowage_factor_unit_id' => 'integer',
            'updated_by_organization' => 'boolean',
            'organization_ids' => 'array',
            'hidden' => 'boolean',
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
            $rules['name'] = 'string';

            return array_map(function($rule) {
                return "nullable|" . $rule;
            }, $rules);
        }

        return $rules;
    }
}
