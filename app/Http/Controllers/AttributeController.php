<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttributeRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Collection;
use JetBrains\PhpStorm\ArrayShape;

class AttributeController extends Controller
{
    public function index()
    {
        return Attribute::all();
    }

    #[ArrayShape(['message' => "string" , 'attribute' => "\App\Models\Attribute|null"])]
    public function store(AttributeRequest $request): array
    {
        return [
            'message'   => 'Create Attribute Successfully' ,
            'attribute' => $this->saveAttribute($request->all()) ,
        ];
    }

    public function show(Attribute $attribute): Attribute
    {
        return $attribute->load('values');
    }

    #[ArrayShape(['message' => "string" , 'attribute' => "\App\Models\Attribute|null"])]
    public function update(AttributeRequest $request , Attribute $attribute): array
    {
        return [
            'message'   => 'Update Attribute Successfully' ,
            'attribute' => $this->saveAttribute($request->all() , $attribute) ,
        ];
    }

    #[ArrayShape(['message' => "string" , 'attribute' => "\App\Models\Attribute"])]
    public function destroy(AttributeRequest $request , Attribute $attribute): array
    {

        if (!$request->has("values")) {
            $attribute->delete();
        } else {
            $attribute = $attribute->load('values');
            $this->deleteAttributeValues($attribute , $request->input('value'));
            $attribute->refresh();
        }

        return [
            'message'   => 'Delete Attribute Successfully' ,
            'attribute' => $attribute ,
        ];
    }

    protected function saveAttribute(array $data , Attribute $attribute = NULL): ?Attribute
    {
        //If Create Attribute
        if ($attribute == NULL) {
            $attribute = new Attribute();
        }
        $attribute->name = $data['name'];
        $attribute->type = $data['type'];
        $attribute->is_variable = $data['is_variable'];
        $attribute->save();

        $this->saveAttributeValues($data['values'] , $attribute);

        return $attribute;
    }

    protected function saveAttributeValues(array $values , Attribute $attribute): Attribute
    {
        $requestIds = collect($values)->pluck('id')->filter(fn($val) => (int)$val)->toArray();
        $deleteValues = array_diff($attribute->values->modelKeys() , $requestIds);
        $this->deleteAttributeValues($attribute , $deleteValues);

        foreach ($values as $value) {
            if (!isset($value['id']) || in_array($value['id'] , ['' , NULL])) {
                $attributeValue = new AttributeValue();
                $attributeValue->attribute_id = $attribute->id;
            } else {
                $attributeValue = $attribute->values->find($value['id']);
            }
            $this->saveAttributeValue($value , $attributeValue , $attribute);
        }
        $attribute->refresh();
        return $attribute;
    }

    protected function deleteAttributeValues(Attribute $attribute , ...$ids)
    {
        !is_array($ids[0]) ?: $ids = $ids[0];
        $attribute->values()->whereIn("id" , $ids)->delete();
    }

    protected function saveAttributeValue(array $value , AttributeValue $attributeValue , Attribute $attribute): AttributeValue
    {
        switch ($attribute->type) {
            case Attribute::TYPE_SIMPLE:
                $attributeValue->value = $value['value'];
                break;
            case Attribute::TYPE_COLOR:
                $attributeValue->value = $value['value'];
                $attributeValue->code = $value['code'];
                break;
            case Attribute::TYPE_UNIT:
                $attributeValue->value = $value['value'];
                $attributeValue->unit = $value['unit'];
                break;
        }
        $attributeValue->save();

        return $attributeValue;
    }

}
