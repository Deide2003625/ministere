<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PressePiece extends Model
{
    use HasFactory;

    protected $table = "presse_pieces";

    protected $fillable = ['cv',
                            'attestation_de_travail',
                            'diplome',
                            'code_requete'];
}
