<?php

namespace App\Models;

use App\Models\MultipleFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecordingRequest extends Model
{
    use HasFactory;

    protected $table = "recording_requests";

    protected $fillable = ['statut_legal',
                        'denomination',
                        'acronyme',
                        'reference_misp',
                        'reference_daic',
                        'arrivee_misp',
                        'arrivee_daic',
                        'date_ag',
                        'departement',
                        'commune',
                        'arrondissement',
                        'quartier',
                        'numero_lot',
                        'immeuble',
                        'boite_postale',
                        'a_telephone',
                        'a_email',
                        'premier_responsable',
                        'email_premier_responsable',
                        'objectifs',
                        'observations',
                        'statut_requete',
                        'matricule_admin',
                        'matricule_super_admin',
                        'code_requete',
                        'respect_du_modele',
                        'insertion_liste_de_presence',
                        'validite_casiers_judiciaires',
                        'verification_membres_presidium',
                        'couverture_regionale',
                        'date_de_creation',
                        'statut_association'];
                        // 'created_at','updated_at'];

    public function MultipleFiles(): HasMany
    {
        return $this->hasMany(MultipleFile::class);
    }
}

