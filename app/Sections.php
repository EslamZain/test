<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sections extends Model
{
    protected $table = 'sections';
    protected $fillable = ['section_name', 'description', 'created_by'];
    protected $hidden = [];

    public function products()
    {
        return $this->hasMany('App\Product', 'section_id', 'id');
    }
    public function invoices()
    {
        return $this->hasMany('App\Invoices', 'section_id', 'id');
    }

}
