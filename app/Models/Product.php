<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;
    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
        'price',
    ];
    // protected $dates = ['deleted_at'];
    // public function delete()
    // {
    //     $this->deleted_at = now();
    //     $this->save();
    // }
}
