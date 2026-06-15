<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'slug'    => 'nullable|string|max:255',
        ];
    }
}
