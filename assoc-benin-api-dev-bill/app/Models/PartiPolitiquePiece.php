<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartiPolitiquePiece extends Model
{
    use HasFactory;

    protected $table = "parti_politique_pieces";

    protected $fillable = ['acte_de_naissance',
                            'certificat_de_nationalite',
                            'code_requete'];
}
