<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    use HasFactory;


    protected $table = 't_programmes';
    protected $primaryKey = 'programme_id';
}
