<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class ProductRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return TRUE;
    }

    public function validationData() {
        $data = $this->all();
        if ( $this->has( 'slug' ) ) {
            $data['slug'] = \Str::slug( $data['slug'] );
        }

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = [
            'name'         => 'required',
            'slug'         => [ 'required', 'unique:products' ],
            'sku'          => [
                'required',
                'regex:/^[A-Z0-9]{6}$/',
                'unique:products',
            ],
            'short_review' => 'required',
            'description'  => 'required',
            'review'       => 'required',
            'type'         => 'required|in:1,2,3',
            'brand_id'     => 'nullable|exists:brands,id',
        ];

        if ( $id = $this->route( 'product' )?->id ) {
            $rules['slug'][1] = 'unique:products,slug,' . $id;
            $rules['sku'][2]  = 'unique:products,sku,' . $id;
        }

        return array_merge(
            $rules,
            $this->links(),
            $this->productAttributes(),
            $this->categories(),
            $this->images(),
            $this->variants(),
        );
    }

    public function links() {
        if ( (int) $this->input( 'type' ) === Product::PRODUCT_TYPE_VIRTUAL ) {
            return [
                'links'               => 'array|required|min:1',
                'links.*'             => 'array',
                'links.*.id'          => 'nullable',
                'links.*.link'        => 'required',
                'links.*.description' => 'nullable',
                'links.*.number'      => 'nullable|distinct',
            ];
        }

        return [];
    }

    public function productAttributes() {
        return [
            'attributes'      => 'required|array',
            'attributes.*.id' => 'required|integer|min:1',
        ];
    }

    private function categories() {
        return [
            'categories'   => 'required|array|min:1',
            'categories.*' => [
                'required',
                Rule::exists( Category::class, 'id' )->where(
                    'type',
                    Category::TYPE_STORE
                ),
            ],
        ];

    }

    private function images() {
        return [
            'images' => 'array|min:1',
        ];
    }

    private function variants() {
        return [
            'variants'                     => 'required|array|min:1' . ( (int) $this->input( 'type' ) !== 2 ? '|max:1' : '' ),
            'variants.*.id'                => 'nullable|integer|min:1',
            'variants.*.purchase_price'    => 'nullable|integer',
            'variants.*.selling_price'     => 'required|integer',
            'variants.*.discounted_price'  => 'nullable|integer',
            'variants.*.wholesale_price'   => 'nullable|integer',
            'variants.*.minimum_wholesale' => 'nullable|integer',
            'variants.*.inventory'         => 'required|integer',
        ];
    }
    public function attributes(){
        return [
            'sku' => 'کد محصول'
        ];
    }

}
