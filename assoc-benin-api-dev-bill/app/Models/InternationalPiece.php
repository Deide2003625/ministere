<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalPiece extends Model
{
    use HasFactory;

    protected $table = "international_pieces";

    protected $fillable = ['duree_mandat',
                            'nom_du_responsable_etranger',
                            'contact_du_responsable_etranger',
                            'mandat',
                            'journal',
                            'recepisse_de_declaration',
                            'rapport_activites',
                            'adresse_benin',
                            'adresse_etranger',
                            'statuts_mere',
                            'reglement_interieur_mere',
                            'code_requete'];
}
