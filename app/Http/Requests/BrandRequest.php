<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
            'name'      => 'required|min:2|max:20' ,
            'slug'      => ['required','min:2','max:20','unique:brands,slug'],
            'logo'      => 'required|array',
            'logo.id'      => 'required|integer|exists:files,id'
        ];

        if ($this->route('brand')){
            $rules['slug'][3]='unique:brands,slug,'.$this->route('brand')->id;
        }

        return $rules;
    }
}
