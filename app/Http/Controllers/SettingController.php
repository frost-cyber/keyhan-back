<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function getHeader(){
        return Setting::where('key' , 'header')->first();
    }
    public function updateHeader(Request $request){
        $request->validate([
            'logo' => 'array',
            'logo.link' => 'required',
            'logo.alt' => 'required',
            'navbar' => 'array',
            'navbar.phone' => 'required',
        ]);
        Setting::where('key' , 'header')->update([
            'key' => 'header',
            'options' => $request->only('logo' , 'navbar'),
        ]);
        return response('Ok');
    }
    public function getFooter(){
        return Setting::where('key' , 'footer')->first();
    }
    public function updateFooter(Request $request){
        $request->validate([
            'contacts' => 'array',
            'contacts.address' => 'required',
            'contacts.phone1' => 'required',
            'contacts.phone2' => 'nullable',
            'contacts.mobile' => 'nullable',
            'socialNetworks' => 'array',
            'socialNetworks.*.icon' => 'required|starts_with:fa-',
            'socialNetworks.*.name' => 'required|string',
            'socialNetworks.*.link' => 'required|url',
            'licenses' => 'array',
            'licenses.*.link' => 'nullable|required_with:licenses.*.src|url',
            'licenses.*.src' => 'nullable',
            'licenses.*.alt' => 'nullable|required_with:licenses.*.src',
            'links' => 'array',
            'links.*.name' => 'required',
            'links.*.link' => 'required|url',
        ]);
        Setting::where('key' , 'footer')->update([
            'options' => $request->only('contacts' , 'socialNetworks' , 'licenses' , 'links'),
        ]);
        return response('Ok');
    }
    public function getHome(){
        return Setting::where('key' , 'home')->first();
    }
    public function updateHome(Request $request){
        $request->validate([
            'slider' => 'array',
            'slider.*.link' => 'required',
            'slider.*.src' => 'required',
            'slider.*.alt' => 'required',
            'sliderBanner' => 'array',
            'sliderBanner.*.link' => 'required',
            'sliderBanner.*.src' => 'required',
            'sliderBanner.*.alt' => 'required',
            'categories' => 'array',
            'categories.*.slug' => 'required',
            'categories.*.src' => 'required',
            'categories.*.alt' => 'required',
            'brands' => 'array',
            'brands.*.slug' => 'required',
            'brands.*.src' => 'required',
            'brands.*.alt' => 'required',

        ]);
        Setting::where('key' , 'home')->update([
            'options' => $request->only('slider' , 'sliderBanner' , 'categories' , 'brands' , 'productsRecommended'),
        ]);
        return response('Ok');
    }
}
