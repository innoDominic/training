<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlottedClasses extends Model
{
    use HasFactory;

    protected $table = 'plotted_classes';
    public $timestamps = false;
    protected $primaryKey = 'plot_no';
    
}
