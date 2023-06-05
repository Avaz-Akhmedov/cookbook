<?php

namespace App\Http\Requests\V1\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            "content" => "required|string",
            "product_uuid" => "required|exists:products,uuid",
            "rating" => "nullable|float|min:1|max:5"
        ];
    }
}