<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teacher';
    public $timestamps = false;

    protected $fillable = [
     'teacher_title',
     'teacher_id',
     'user_no',
    ];
}
