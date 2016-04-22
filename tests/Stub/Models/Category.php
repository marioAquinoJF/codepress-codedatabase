<?php

namespace CodePress\CodeDatabase\Tests\Stub\Models;

use Illuminate\Database\Eloquent\Model;


class Category extends Model
{

    protected $table = "code_categories";
  
    protected $fillable = [
        'name',
        'description'
    ];

}
