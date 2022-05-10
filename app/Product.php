<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    // protected $fillable = ['product_name', 'describtion', 'section_id'];
    protected $hidden = [];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function sections()
    {

        return $this->belongsTo('App\Sections', 'section_id', 'id');
    }

}
