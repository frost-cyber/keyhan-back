<?php

namespace App\Http\Requests;

use App\Models\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeRequest extends FormRequest
{

    private $validationRoles;

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

        if ($this->isMethod("delete")) {
            $this->rulesDelete();
        } else {
            $this->rulesCreateAndUpdate();
        }
        return $this->validationRoles;
    }

    private function rulesDelete()
    {
        $this->validationRoles = [
            'values'   => 'array' ,
            'values.*' => 'integer|min:1|distinct' ,
        ];
    }

    private function rulesCreateAndUpdate()
    {
        $this->validationRoles = [
            'name'        => 'required|min:2' ,
            'is_variable' => 'required|boolean' ,
            'values'      => 'array' ,
        ];

        $this->validationRoles['type'] = [
            'required' ,
            Rule::in([Attribute::TYPE_SIMPLE , Attribute::TYPE_COLOR , Attribute::TYPE_UNIT]) ,
        ];
        if ($this->has('type')) {
            switch ($this->input('type')) {
                case Attribute::TYPE_SIMPLE:
                    $this->validationRoles['values.*.value'] = 'required|min:2';
                    break;
                case Attribute::TYPE_COLOR:
                    $this->validationRoles['values.*.value'] = 'required|min:2';
                    $this->validationRoles['values.*.code'] = 'required|min:2';
                    break;
                case Attribute::TYPE_UNIT:
                    $this->validationRoles['values.*.value'] = 'required|min:2';
                    $this->validationRoles['values.*.unit'] = 'required|min:2';
                    break;
            }
        }
    }

    public function attributes()
    {
        $attributes = [
            'values.*.code'  => 'کد رنگ' ,
            'values.*.unit' => 'واحد' ,
        ];
        if ($this->has('type')) {
            switch ($this->input('type')) {
                case Attribute::TYPE_UNIT:
                case Attribute::TYPE_SIMPLE:
                    $attributes['values.*.value'] = 'مقدار';
                    break;
                case Attribute::TYPE_COLOR:
                    $attributes['values.*.value'] = 'نام رنگ';
                    break;
            }
        }
        return $attributes;
    }
}
