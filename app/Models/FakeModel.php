<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FakeModel extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    protected static function booted()
    {
        static::addGlobalScope('only_fake', function ($builder) {
            $builder->getQuery()->fromRaw('(select 1 as id, "A1" as codigo, "Hola" as mensaje union all select 2, "B2", "Mundo" union all select 3, "C3", "Cómo estás") as fake_table');
        });
    }
}
