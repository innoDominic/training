<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlottedClassesTeacher extends Model
{
    use HasFactory;

    protected $table = 'plotted_classes_teacher';
    public $timestamps = false;
    protected $primaryKey = 'plot_no_teacher';
    
}
