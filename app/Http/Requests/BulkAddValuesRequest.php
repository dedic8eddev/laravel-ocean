<?php

namespace App\Http\Requests;

use App\Models\MarketIndex;

class BulkAddValuesRequest extends JsonRequest
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
        return [
            "date" => "required|date",
            "index" => "required|array|exists_property:market_index_id,market_indexes,id",
            "index.*.market_index_id" => "required|integer",
            "index.*.value" => "required|integer",
        ];
    }
}
