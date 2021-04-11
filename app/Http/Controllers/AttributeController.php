<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttributeRequest;
use App\Models\Attribute;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::query();

        if (request()->has('groupBy')){
            $groupBy = request('groupBy');

            $groupBys = ['name' , 'type' , 'is_variable'];

            if (!is_array($groupBy)){
                $groupBy = [$groupBy];
            }
            if (!count(array_diff($groupBy , $groupBys))){
                $attributes = $attributes->select( '*',\DB::raw('count(id) as countAttributes'))->groupBy($groupBy);
            }
        }

        if (request()->has('name')){
            $attributes = $attributes->whereName(request('name'));
        }

        if (request()->has('type')){
            $attributes = $attributes->whereType((int) request('type'));
        }

        if (request()->has('name')){
            $attributes = $attributes->where('is_variable' , (boolean)request( 'name'));
        }
        return $attributes->get();
    }

    public function store(AttributeRequest $request): array
    {
        return [
            'message'   => 'Create Attribute Successfully' ,
            'attribute' =>  $this->saveAttribute($request->all())
        ];
    }

    public function show(Attribute $attribute): Attribute
    {
        return $attribute;
    }

    public function update(AttributeRequest $request , Attribute $attribute): array
    {
        return [
            'message'   => 'Update Attribute Successfully' ,
            'attribute' =>  $this->saveAttribute($request->all() , $attribute)
        ];
    }

    public function destroy(Attribute $attribute): array
    {
        if (request()->has("group")) {
            Attribute::where('name' , $attribute->name)->delete();
        } else {
            $attribute->delete();
        }

        return [
            'message'   => 'Delete Attribute Successfully' ,
        ];
    }

    protected function saveAttribute(array $data , Attribute $attribute = null ): Attribute
    {
        //If Create Attribute
        if ($attribute == NULL) {
            $attribute = new Attribute();
        }

        $attribute->name = $data['name'];
        $attribute->type = $data['type'];
        $attribute->is_variable = (boolean) $data['is_variable'];

        switch ($attribute->type) {
            case Attribute::TYPE_SIMPLE:
                $attribute->value = $data['value'];
                break;
            case Attribute::TYPE_COLOR:
                $attribute->value = $data['value'];
                $attribute->code = $data['code'];
                break;
            case Attribute::TYPE_UNIT:
                $attribute->value = $data['value'];
                $attribute->unit = $data['unit'];
                break;
        }
        $attribute->save();

        return $attribute;
    }
}
