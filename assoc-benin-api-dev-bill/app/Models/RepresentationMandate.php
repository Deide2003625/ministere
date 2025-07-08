<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepresentationMandate extends Model
{
    use HasFactory;

    protected $table = "representation_mandates";
    // Reconvertie
    protected $fillable = ['mandat',
                            'journal',
                            'rapport_activites',
                            'adresse_benin',
                            'adresse_etranger',
                            'code_requete'];

}