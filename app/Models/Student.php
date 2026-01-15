<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'course_name',
        'start_date',
        'end_date',
        'rate',
        'score',

    ];
}
