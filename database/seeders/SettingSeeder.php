<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::query()->delete();
        Setting::create($this->header());
        Setting::create($this->footer());
    }
    private function header(){
        return [
            'key' => 'header',
            'options'=>[
                'logo' => [
                    'link' => null,
                    'alt' => null,
                ], 
                'navbar'=>[]
                ]
            ];
    }
    private function footer(){
        return [
            'key' => 'footer' ,
            'options' => [
                'contacts' => [
                    'address' => null,
                    'phone1' => null,
                    'phone2' => null,
                    'mobile' => null
                ],
                'socialNetworks' => [],
                'licenses' => [
                    [
                        'link' => null ,
                        'src' => null , 
                        'alt' => null ,
                    ],
                    [
                        'link' => null ,
                        'src' => null , 
                        'alt' => null ,
                    ],
                    [
                        'link' => null ,
                        'src' => null , 
                        'alt' => null ,
                    ],
                ],
                'links' => []
            ]
        ];
    }
}
