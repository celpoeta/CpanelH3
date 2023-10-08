<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Zoo extends Model
{
    use HasFactory;
    use HasSlug;

    public $fillable = [
        'scientific_name', 'common_name', 'description','risk', 'risk_description','distribution', 'habitat', 'url_video', 'url_image','category_id', 'slug', 'status' ,'created_at','created_by'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('scientific_name')
            ->saveSlugsTo('slug');
    }
}
