<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name'                         => 'required' ,
            'slug'                         => ['required' , 'unique:products'] ,
            'sku'                          => ['required' , 'regex:/^[A-Z0-9]{6}/i' , 'unique:products'] ,
            'short_review'                 => 'required' ,
            'description'                  => 'required' ,
            'review'                       => 'required' ,
            'type'                         => 'required|in:1,2,3' ,
            'brand_id'                     => 'nullable|exists:brands,id' ,
            'categories'                   => 'required|array' ,
            'attributes'                   => 'required|array' ,
            'attributes.*.id'              => 'required|integer|min:1' ,
            'variants'                     => 'required|array' ,
            'variants.*.id'                => 'nullable|integer|min:1' ,
            'variants.*.purchase_price'    => 'nullable|integer' ,
            'variants.*.selling_price'     => 'required|integer' ,
            'variants.*.discounted_price'  => 'nullable|integer' ,
            'variants.*.wholesale_price'   => 'nullable|integer' ,
            'variants.*.minimum_wholesale' => 'nullable|integer' ,
            'variants.*.inventory'         => 'required|integer' ,
        ];

        if ($id = $this->route('product')?->id) {
            $rules['slug'][1] = 'unique:products,slug,' . $id;
            $rules['sku'][2] = 'unique:products,sku,' . $id;

        }



        $rules['categories.*'] = [
            'required' ,
            Rule::exists(Category::class , 'id')->where('type' , Category::TYPE_STORE),
        ];
        print_r($rules);
        return $rules;
    }

    public function validationData()
    {
        $data = $this->all();
        if ($this->has('slug')) {
            $data['slug'] = \Str::slug($data['slug']);
        }
        return $data;
    }
}
