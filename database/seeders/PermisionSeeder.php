<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Permission::query()->delete();
	    $data=[];
	    $models =['articles','products','posts'];
	    $permisions=['read','create','update','delete'];
	    foreach($permisions as $permision){
		    foreach ($models as $model){
			    $data[]["name"]=  $permision . ' '. $model;
		    }
	    }
	    Permission::insert($data);
    }
}
