<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class FileController extends Controller {

    const METHODS = [
        'ProductImage',
        'EditorImage',
        'BrandLogo',
        'ArticleThumbnail',
        'SettingSiteLogo',
        'SettingLicenseImage',
        'HomeSliderImage',
        'HomeSliderBannerImage',
        'HomeBrandImage',
        'HomeCategoryImage',
        'ProfileAvatar',
    ];

    private $rules;

    private $uploadOptoins = [];
    private $customMessages = [];
    private $customAttributes = [];

    private $file;

    public function index() {

    }

    public function upload( Request $request ) {
        if ( ! $request->query( 'for' ) ) {
            throw new BadRequestException( "Bad Call" );
        }

        if ( ! $request->hasFile( 'file' ) ) {
            throw new BadRequestException( "Bad Call" );
        }

        $this->SetupUpload( $request->file( 'file' ), $request->query( 'for' ) );

        $request->validate( $this->rules, $this->customMessages, $this->customAttributes );

        return $this->saveFile( $this->storeFile(), new File() );

    }

    public function delete() {
    }

    private function setupProductImageUpload() {
        $this->rules = [
            'file' => 'required|mimetypes:image/jpg,image/png,image/jpeg',
        ];

        $this->uploadOptoins['path'] = '/product/images';
        $this->uploadOptoins['type'] = 1;
        $this->uploadOptoins['disk'] = 'public';
    }

    private function setupHomeSliderImageUpload() {
        $this->rules = [
            'file' => 'required|mimetypes:image/jpg,image/png,image/jpeg'
        ];

        $this->uploadOptoins['path'] = '/home/slider';
        $this->uploadOptoins['type'] = 1;
        $this->uploadOptoins['disk'] = 'public';
    }

    private function setupHomeSliderBannerImageUpload() {
        $this->rules = [
            'file' => 'required|mimetypes:image/jpg,image/png,image/jpeg'
        ];

        $this->uploadOptoins['path'] = '/home/slider/banners';
        $this->uploadOptoins['type'] = 1;
        $this->uploadOptoins['disk'] = 'public';
    }

    private function setupHomeCategoryImageUpload() {
        $this->rules = [
            'file' => 'required|mimetypes:image/jpg,image/png,image/jpeg'
        ];

        $this->uploadOptoins['path'] = '/home/categories';
        $this->uploadOptoins['type'] = 1;
        $this->uploadOptoins['disk'] = 'public';
    }

    private function setupHomeBrandImageUpload() {
        $this->rules = [
            'file' => 'required|mimetypes:image/jpg,image/png,image/jpeg'
        ];

        $this->uploadOptoins['path'] = '/home/brands';
        $this->uploadOptoins['type'] = 1;
        $this->uploadOptoins['disk'] = 'public';
    }

    private function setupEditorImageUpload() {
        $this->rules = [
            'file' => 'required|mimetypes:image/jpg,image/png,image/jpeg',
        ];

        $this->uploadOptoins['path'] = '/editor/images';
        $this->uploadOptoins['type'] = 1;
        $this->uploadOptoins['disk'] = 'public';
    }

    private function setupBrandLogoUpload() {
        $this->rules = [
            'file' => 'required|mimetypes:image/jpg,image/png,image/jpeg,image/svg,image/svg+xml',
        ];

        $this->uploadOptoins['path'] = '/brands/logo';
        $this->uploadOptoins['type'] = 2;
        $this->uploadOptoins['disk'] = 'public';
    }

    private function setupArticleThumbnailUpload() {
        $this->rules                 = [
            'file' => 'required|mimetypes:image/jpg,image/png,image/jpeg,image/svg,image/svg+xml',
        ];
        $this->customAttributes      = [ 'file' => 'شاخص' ];
        $this->uploadOptoins['path'] = '/article/images';
        $this->uploadOptoins['type'] = 1;
        $this->uploadOptoins['disk'] = 'public';
    }

    private function setupSettingSiteLogoUpload() {
        $this->rules = [
            'file' => 'required|mimetypes:text/plain,image/jpg,image/png,image/jpeg,image/svg,image/svg+xml'
        ];

        $this->uploadOptoins['path'] = '/logo';
        $this->uploadOptoins['type'] = 1;
        $this->uploadOptoins['disk'] = 'public';
    }

    private function setupSettingLicenseImageUpload() {
        $this->rules = [
            'file' => 'required|mimetypes:image/jpg,image/png,image/jpeg'
        ];

        $this->uploadOptoins['path'] = '/licenses';
        $this->uploadOptoins['type'] = 1;
        $this->uploadOptoins['disk'] = 'public';
    }

    private function setupProfileAvatarUpload() {
        $this->rules                 = [
            'file' => 'required|mimetypes:image/jpg,image/png,image/jpeg',
        ];
        $this->customAttributes      = [ 'file' => 'آواتار' ];
        $this->uploadOptoins['path'] = '/profile/images';
        $this->uploadOptoins['type'] = 3;
        $this->uploadOptoins['disk'] = 'public';
    }

    private function SetupUpload( UploadedFile $file, string $for ) {
        $for = \Str::Studly( $for );

        $this->uploadOptoins['extension'] = $file->getExtension();
        $this->uploadOptoins['name']      = $file->getBasename();
        $this->file                       = $file;

        if ( ! in_array( $for, static::METHODS ) ) {
            throw new \BadFunctionCallException( "Failed" );
        }

        $this->{"setup{$for}Upload"}();
    }

    private function saveFile( array $data, File $file ): File {
        $file->name      = $data['name'];
        $file->extension = $data['extension'];
        $file->type      = $data['type'];
        $file->path      = $data['path'];
        $file->link      = $data['link'];

        $file->save();

        return $file;
    }

    private function storeFile(): array {
        $file = $this->file->storeAs(
            $this->uploadOptoins['path'] ?? '/',
            $this->file->hashName(),
            $this->uploadOptoins['disk'] ?? [],
        );

        return [
            'name'      => $this->file->getClientOriginalName(),
            'extension' => $this->file->extension(),
            'type'      => $this->uploadOptoins['type'],
            'path'      => $file,
            'link'      => Storage::disk( $this->uploadOptoins['disk'] )->url( $file ),
        ];
    }
}
