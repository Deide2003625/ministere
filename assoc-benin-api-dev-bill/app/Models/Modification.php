<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modification extends Model
{
    use HasFactory;

    protected $table = "modifications";

    protected $fillable = ['etat_modification',
                            'code_requete'
                        ];
}
