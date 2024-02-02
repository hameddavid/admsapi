<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreeningSchedule extends Model
{
    use HasFactory;

    protected $table = 't_screening_schedules';
    protected $primaryKey = 'id';
}
