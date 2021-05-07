<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLink extends Model {
    use HasFactory;

    protected $fillable = [ 'title', 'link', 'description', 'number' ];

    public function product() {
        return $this->belongsTo( Product::class );
    }
}
