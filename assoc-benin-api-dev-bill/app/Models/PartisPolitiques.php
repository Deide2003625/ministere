<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartisPolitiques extends Model
{
    use HasFactory;

    protected $table = "partis_politiques";

    protected $fillable = ['declaration',
                            'liste_de_presence',
                            'liste_de_createurs',
                            'projets_de_societe',
                            'fiche_de_description',
                            'logo_et_embleme',
                            'ideologie',
                            'code_requete'];
}
