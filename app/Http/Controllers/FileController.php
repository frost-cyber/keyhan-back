<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{

    private $rules;
    private $uploadSetup;

    public function index()
    {

    }

    public function upload(Request $request)
    {

    }

    public function delete()
    {

    }

    private function ValidateFiles(Request $request)
    {
        $request->validate($this->rules);
    }

    private function setupProductFileUpload()
    {
        $this->rules = [];
        $this->uploadSetup = [];
    }

    private function setupArticleThumbnailUpload()
    {

    }
}
