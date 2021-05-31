<?php

namespace App\Http\Requests;

use App\Models\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $roles = [
            'name'        => 'required|min:2' ,
            'is_variable' => 'required|boolean' ,
        ];

        if($this->has('routeCreate')){
           $roles['name'] = '|unique:attributes';
        }

        $roles['type'] = [
            'required' ,
            Rule::in([Attribute::TYPE_SIMPLE , Attribute::TYPE_COLOR , Attribute::TYPE_UNIT]) ,
        ];

        if ($this->has('type')) {
            switch ($this->input('type')) {
                case Attribute::TYPE_SIMPLE:
                    $roles['value'] = 'required|min:2';
                    break;
                case Attribute::TYPE_COLOR:
                    $roles['value'] = 'required|min:2|alpha';
                    $roles['code'] = 'required|min:2';
                    break;
                case Attribute::TYPE_UNIT:
                    $roles['value'] = 'required|min:2';
                    $roles['unit'] = 'required|min:2';
                    break;
            }
        }

        return $roles;
    }

    public function attributes()
    {
        $attributes = [
            'code'  => 'کد رنگ' ,
            'unit' => 'واحد' ,
        ];
        if ($this->has('type')) {
            switch ($this->input('type')) {
                case Attribute::TYPE_UNIT:
                case Attribute::TYPE_SIMPLE:
                    $attributes['value'] = 'مقدار';
                    break;
                case Attribute::TYPE_COLOR:
                    $attributes['value'] = 'نام رنگ';
                    break;
            }
        }
        return $attributes;
    }
}
