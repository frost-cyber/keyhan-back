<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table( 'settings' )->truncate();
        Setting::create( $this->header() );
        Setting::create( $this->footer() );
        Setting::create( $this->home() );
    }

    private function header() {
        return [
            'key'     => 'header',
            'options' => [
                "logo"   => [
                    "link" => "/_nuxt/assets/img/logo.svg",
                    "alt"  => "کافه انرژی",
                ],
                "navbar" => [
                    "phone" => "09120760346",
                ],
            ],
        ];
    }

    private function home() {
        return [
            'key'     => 'home',
            'options' => [
                "slider"              => [
                    [
                        "link" => "/",
                        "src"  => "/slider/slider01.jpg",
                        "alt"  => "Slide 1",
                    ],
                    [
                        "link" => "/",
                        "src"  => "/slider/slider01.jpg",
                        "alt"  => "Slide 2",
                    ],
                    [
                        "link" => "/",
                        "src"  => "/slider/slider01.jpg",
                        "alt"  => "Slide 3",
                    ],
                ],
                "sliderBanners"       => [
                    [
                        "link" => "/",
                        "src"  => "/_nuxt/assets/img/banner/banner01.jpg",
                        "alt"  => "Banner 1",
                    ],
                    [
                        "link" => "/",
                        "src"  => "/_nuxt/assets/img/banner/banner02.jpg",
                        "alt"  => "Banner 2",
                    ],
                ],
                "categories"          => [
                    [
                        "slug" => "sadfas",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/technology.svg",
                        "alt"  => "1",
                    ],
                    [
                        "slug" => "asdfasdf",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/transmitting.svg",
                        "alt"  => "2",
                    ],
                    [
                        "slug" => "dddd",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/electrical-panel.svg",
                        "alt"  => "3",
                    ],
                    [
                        "slug" => "dsfsa",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/power-plant.svg",
                        "alt"  => "4",
                    ],
                    [
                        "slug" => "r21214",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/smarthouse.svg",
                        "alt"  => "5",
                    ],
                    [
                        "slug" => "shsybshs",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/energy.svg",
                        "alt"  => "6",
                    ],
                ],
                "brands"              => [
                    [
                        "slug" => "Apple",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/brands/Ansee.png",
                        "alt"  => "1",
                    ],
                    [
                        "slug" => "Apple0",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/brands/cantonk.png",
                        "alt"  => "2",
                    ],
                    [
                        "slug" => "Apple",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/brands/farahoosh.png",
                        "alt"  => "3",
                    ],
                    [
                        "slug" => "Apple0",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/brands/mrsmart.png",
                        "alt"  => "4",
                    ],
                    [
                        "slug" => "Apple",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/brands/starvedia.png",
                        "alt"  => "5",
                    ],
                    [
                        "slug" => "Apple0",
                        "src"  => "http://keyhan.p/_nuxt/assets/img/brands/Ansee.png",
                        "alt"  => "6",
                    ],
                ],
                "productsRecommended" => [
                    "categories" => [
                        "dsfsa",
                    ],
                    "products"   => [
                        "test-product",
                    ],
                ],
            ],
        ];
    }

    private function footer() {
        return [
            'key'     => 'footer',
            'options' => [
                "contacts"       => [
                    "address" => "بیرجند - خیابان شهدا - شهدا 5 - پلاک 2 -واحد 12",
                    "phone1"  => "056-32210006",
                    "phone2"  => "056-32210007",
                    "mobile"  => "09120760345",
                ],
                "socialNetworks" => [
                    [
                        "icon" => "fa-facebook",
                        "name" => "فیسبوک",
                        "link" => "http://facebook.com",
                    ],
                    [
                        "icon" => "fa-telegram",
                        "name" => "تلگرام",
                        "link" => "http://telegram.org",
                    ],
                ],
                "licenses"       => [
                    [
                        "link" => "http://keyhan.p/",
                        "src"  => "/storage/licenses/5gF9P52szgFmTxziyzKsI5jbIe2pcXtZEEoLbzmS.png",
                        "alt"  => "کافه انرژی",
                    ],
                    [
                        "link" => "http://keyhan.p/",
                        "src"  => "/storage/licenses/mvZkQVtVb0xvXNUo2Pmh8oYAkRCOMh9uOJ7cgMBO.png",
                        "alt"  => "کافه انرژی",
                    ],
                    [
                        "link" => "http://keyhan.p/",
                        "src"  => "/storage/licenses/w1KDwRZdkVTLPL3WfbS5MGytY0SApnobt83dA2c4.png",
                        "alt"  => "کافه انرژی",
                    ],
                ],
                "links"          => [
                    [
                        "link" => "http://keyhan.p/",
                        "name" => "ارتباط با ما",
                    ],
                    [
                        "link" => "http://keyhan.p/",
                        "name" => "تماس با ما",
                    ],
                    [
                        "link" => "http://keyhan.p/",
                        "name" => "قوانین و مقررات",
                    ],
                ],
            ],
        ];
    }
}
