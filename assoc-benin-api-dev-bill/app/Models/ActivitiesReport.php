<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitiesReport extends Model
{
    use HasFactory;

    protected $table = "activities_reports";

    // Non utilisée
    protected $fillable = ['report',
                            'report_age',
                            'code_requete'];
}
