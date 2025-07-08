<?php

namespace App\Http\Controllers;

use App\Models\MiscFile;
use App\Models\PressePiece;
// use App\Http\Controllers\DB;
use Illuminate\Support\Str;
use App\Models\MultipleFile;
use Illuminate\Http\Request;
use App\Models\CriminalRecord;
use App\Mail\SendingRequestCode;
use App\Models\ActivitiesReport;
use App\Models\GlobalAttachment;
use App\Models\PartisPolitiques;
use App\Models\RecordingRequest;
use App\Models\InternationalPiece;
use Illuminate\Support\Facades\DB;
use App\Models\PartiPolitiquePiece;
use App\Models\ResidenceCertificate;
use Illuminate\Support\Facades\Mail;
use App\Models\RepresentationMandate;
use Illuminate\Support\Facades\Storage;

class RecordingRequestsController extends Controller
{
    
    /**
     * Get all requests which are still examining.
     *
     * @return \Illuminate\Http\Response
     */
    public function GetInProgress($admin_registration_number)
    {
        // return response()->json([
        //     "status" => 1,
        //     "records" => "Blab."
        // ]);
        $admin_data = DB::connection("mysql")->table('users')->where('matricule',$admin_registration_number)->first();

        if($admin_data->departement == "tous")
        {
            $records = DB::connection("mysql")->table('recording_requests')
            // ->whereIn('recording_requests.statut_requete',["en cours de traitement","rejetee","pre rejetee"])
            ->where('recording_requests.statut_requete',"en cours de traitement")
            ->leftjoin('international_pieces','international_pieces.code_requete','=','recording_requests.code_requete')
            ->leftjoin('partis_politiques','partis_politiques.code_requete','=','recording_requests.code_requete')
            ->leftjoin('parti_politique_pieces','parti_politique_pieces.code_requete','=','recording_requests.code_requete')
            ->leftjoin('presse_pieces','presse_pieces.code_requete','=','recording_requests.code_requete')
            ->leftjoin('modifications','modifications.code_requete','=','recording_requests.code_requete')
            // ->leftjoin('representation_mandates','representation_mandates.code_requete','=','recording_requests.code_requete')
            // ->leftjoin('users','users.matricule','=','recording_requests.matricule_admin')
            ->select([
                // recording_requests
                'recording_requests.statut_legal','recording_requests.denomination','recording_requests.acronyme',
                'recording_requests.reference_misp','recording_requests.reference_daic','recording_requests.arrivee_misp',
                'recording_requests.arrivee_daic','recording_requests.date_ag','recording_requests.departement',
                'recording_requests.commune','recording_requests.arrondissement','recording_requests.quartier',
                'recording_requests.numero_lot','recording_requests.immeuble','recording_requests.boite_postale',
                'recording_requests.a_telephone','recording_requests.a_email','recording_requests.premier_responsable',
                'recording_requests.email_premier_responsable','recording_requests.objectifs','recording_requests.observations',
                'recording_requests.statut_requete','recording_requests.matricule_admin','recording_requests.matricule_super_admin','recording_requests.code_requete',
                'recording_requests.respect_du_modele','recording_requests.insertion_liste_de_presence',
                'recording_requests.validite_casiers_judiciaires','recording_requests.verification_membres_presidium','recording_requests.couverture_regionale',
                'recording_requests.date_de_creation','recording_requests.statut_association','recording_requests.created_at',
                'recording_requests.updated_at',
                // criminal_records
                // 'criminal_records.casier','criminal_records.role_du_membre',
                // // international_pieces
                // 'international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                // 'international_pieces.rapport_activites','international_pieces.adresse_benin','international_pieces.adresse_etranger',
                // 'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                'international_pieces.duree_mandat','international_pieces.nom_du_responsable_etranger','international_pieces.contact_du_responsable_etranger',
                // // parti_politique_pieces 
                // 'parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',
                // partis_politiques
                // 'partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                // 'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',
                'partis_politiques.ideologie',
                // // presse_pieces
                // 'presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',
                // residence_certificates
                // 'residence_certificates.certificat','residence_certificates.role_du_membre',
                // representation_mandates
                // 'representation_mandates.mandat','representation_mandates.journal','representation_mandates.rapport_activites',
                // 'representation_mandates.adresse_benin','representation_mandates.adresse_etranger',
                // modifications
                'modifications.etat_modification',
                // users
                // 'users.matricule','users.prenom','users.nom','users.u_telephone','users.u_email','users.role','users.statut',
                // global_attachments
                // 'global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                // 'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                // 'global_attachments.recepisse_admin','global_attachments.statuts',
                // multiples_files
                // 'multiples_files.casier_judiciaire','multiples_files.certificat_de_residence',
                // 'multiples_files.role_du_membre',
                ])
            ->orderBy('recording_requests.id', 'desc')
            ->paginate(10);
            // ->get();
            if (! empty($records))
            {
                foreach ($records as $r)
                {
                    // if ($r->code_requete == "78A3DE2D") {
                    //     // $multiple_files = array();
                    //     $multiple_files = DB::connection("mysql")->table('multiple_files')
                    //                         ->where('code_requete',$r->code_requete)
                    //                         ->select('*')->get()->groupBy('type');
                    //     // $multiple = json_decode(json_encode($multiple_files), true);
                    //     $r->multiple_files = $multiple_files;
                    //     return response()->json([
                    //         "status" => 1,
                    //         "records" => $r
                    //     ]);
                    // }

                    // dd($r);
                    // die(gettype($r));

                    # Multiples files
                    // $multiple_files = array();
                    // return response()->json([
                    //     "status" => 1,
                    //     "records" => $r
                    // ]);
                    $multiple_files = DB::connection("mysql")->table('multiple_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->select('*')->get()->groupBy('type');
                    $r->multiple_files = $multiple_files;

                    $misc_files = DB::connection("mysql")->table('misc_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->get();
                    $r->misc_files = $misc_files;
                    // $a1 = array();
                    // $a1 = DB::connection("mysql")->table('multiple_files')
                    // ->where([['code_requete',$r->code_requete],['type','casier judiciaire']])
                    // ->select(['multiple_files.type','multiple_files.file',])
                    // ->get();
                    // $aa1 = json_decode(json_encode($a1), true);
                    // $r->criminal_records = $aa1;
                    // $a2 = array();
                    // $a2 = DB::connection("mysql")->table('multiple_files')
                    // ->where([['code_requete',$r->code_requete],['type','certificat de residence']])
                    // ->select(['multiple_files.type','multiple_files.file',])
                    // ->get();
                    // $aa2 = json_decode(json_encode($a2), true);
                    // $r->residence_certificates = $aa2;
                    // $a3 = array();
                    // $a3 = DB::connection("mysql")->table('multiple_files')
                    // ->where([['code_requete',$r->code_requete],['type','acte de naissance']])
                    // ->select(['multiple_files.type','multiple_files.file',])
                    // ->get();
                    // $aa3 = json_decode(json_encode($a3), true);
                    // $r->birth_certificates = $aa3;
                    // $a4 = array();
                    // $a4 = DB::connection("mysql")->table('multiple_files')
                    // ->where([['code_requete',$r->code_requete],['type','certificat de nationalite']])
                    // ->select(['multiple_files.type','multiple_files.file',])
                    // ->get();
                    // $aa4 = json_decode(json_encode($a4), true);
                    // $r->nationality_certificates = $aa4;

                    # Global attachments
                    // $b = array();
                    $global_attachments = DB::connection("mysql")->table('global_attachments')
                            ->where('code_requete',$r->code_requete)
                            ->select(['global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                                'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                                'global_attachments.recepisse_admin','global_attachments.ancien_recepisse_admin','global_attachments.statuts',])
                            ->first();
                    $r->global_attachments = $global_attachments;

                    # Observations
                    $c = array();
                    $c = DB::connection("mysql")->table('observations')
                            ->where('code_requete',$r->code_requete)
                            ->leftjoin('statistics','statistics.id','=','observations.statistic_id')
                            ->select(['statistics.id','statistics.element',])
                            ->get();
                    $cc = json_decode(json_encode($c), true);
                    $r->observations = $cc;

                    # International pieces
                    $d = array();
                    $d = DB::connection("mysql")->table('international_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                                'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                                'international_pieces.rapport_activites','international_pieces.statuts_mere','international_pieces.reglement_interieur_mere'])
                    ->get();
                    $dd = json_decode(json_encode($d), true);
                    $r->international_pieces = $dd;

                    # Partis politiques pieces
                    $e = array();
                    $e = DB::connection("mysql")->table('parti_politique_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',])
                    ->get();
                    $ee = json_decode(json_encode($e), true);
                    $r->parti_politique_pieces = $ee;

                    # Partis politiques
                    $f = array();
                    $f = DB::connection("mysql")->table('partis_politiques')
                    ->where('code_requete',$r->code_requete)
                    ->select(['partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                    'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',])
                    ->get();
                    $ff = json_decode(json_encode($f), true);
                    $r->partis_politiques = $ff;

                    # Presse pieces
                    $g = array();
                    $g = DB::connection("mysql")->table('presse_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',])
                    ->get();
                    $gg = json_decode(json_encode($g), true);
                    $r->presse_pieces = $gg;

                    # Representation mandates
                    // $h = array();
                    // $h = DB::connection("mysql")->table('representation_mandates')
                    // ->where('code_requete',$r->code_requete)
                    // ->select(['representation_mandates.mandat','representation_mandates.journal','representation_mandates.rapport_activites',])
                    // ->get();
                    // $hh = json_decode(json_encode($h), true);
                    // $r->representation_mandates = $hh;
                }
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "records" => "empty"
                ]);
            }
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
        else
        {
            $records = DB::connection("mysql")->table('recording_requests')
            ->where([['recording_requests.statut_requete','en cours de traitement'],['recording_requests.departement',$admin_data->departement]])
            ->leftjoin('international_pieces','international_pieces.code_requete','=','recording_requests.code_requete')
            ->leftjoin('partis_politiques','partis_politiques.code_requete','=','recording_requests.code_requete')
            ->leftjoin('parti_politique_pieces','parti_politique_pieces.code_requete','=','recording_requests.code_requete')
            ->leftjoin('presse_pieces','presse_pieces.code_requete','=','recording_requests.code_requete')
            ->leftjoin('modifications','modifications.code_requete','=','recording_requests.code_requete')
            ->select([
                'recording_requests.statut_legal','recording_requests.denomination','recording_requests.acronyme',
                'recording_requests.reference_misp','recording_requests.reference_daic','recording_requests.arrivee_misp',
                'recording_requests.arrivee_daic','recording_requests.date_ag','recording_requests.departement',
                'recording_requests.commune','recording_requests.arrondissement','recording_requests.quartier',
                'recording_requests.numero_lot','recording_requests.immeuble','recording_requests.boite_postale',
                'recording_requests.a_telephone','recording_requests.a_email','recording_requests.premier_responsable',
                'recording_requests.email_premier_responsable','recording_requests.objectifs','recording_requests.observations',
                'recording_requests.statut_requete','recording_requests.matricule_admin','recording_requests.matricule_super_admin','recording_requests.code_requete',
                'recording_requests.respect_du_modele','recording_requests.insertion_liste_de_presence',
                'recording_requests.validite_casiers_judiciaires','recording_requests.verification_membres_presidium','recording_requests.couverture_regionale',
                'recording_requests.date_de_creation','recording_requests.statut_association','recording_requests.created_at',
                'recording_requests.updated_at',
                // 'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                'international_pieces.duree_mandat','international_pieces.nom_du_responsable_etranger','international_pieces.contact_du_responsable_etranger',
                'partis_politiques.ideologie',
                'modifications.etat_modification',
            ])
            ->orderBy('recording_requests.id', 'desc')
            ->paginate(10);
            // ->get();
            if (! empty($records))
            {
                foreach ($records as $r)
                {

                    # Multiples files
                    $multiple_files = DB::connection("mysql")->table('multiple_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->select('*')->get()->groupBy('type');
                    $r->multiple_files = $multiple_files;

                    $misc_files = DB::connection("mysql")->table('misc_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->get();
                    $r->misc_files = $misc_files;+

                    # Global attachments
                    $global_attachments = DB::connection("mysql")->table('global_attachments')
                            ->where('code_requete',$r->code_requete)
                            ->select(['global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                                'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                                'global_attachments.recepisse_admin','global_attachments.ancien_recepisse_admin','global_attachments.statuts',])
                            ->first();
                    $r->global_attachments = $global_attachments;

                    # Observations
                    $c = array();
                    $c = DB::connection("mysql")->table('observations')
                    ->where('code_requete',$r->code_requete)
                    ->leftjoin('statistics','statistics.id','=','observations.statistic_id')
                    ->select(['statistics.id','statistics.element',])
                    ->get();
                    $cc = json_decode(json_encode($c), true);
                    $r->observations = $cc;

                    # International pieces
                    $d = array();
                    $d = DB::connection("mysql")->table('international_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                                'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                                'international_pieces.rapport_activites','international_pieces.statuts_mere','international_pieces.reglement_interieur_mere',])
                    ->get();
                    $dd = json_decode(json_encode($d), true);
                    $r->international_pieces = $dd;

                    # Partis politiques pieces
                    $e = array();
                    $e = DB::connection("mysql")->table('parti_politique_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',])
                    ->get();
                    $ee = json_decode(json_encode($e), true);
                    $r->parti_politique_pieces = $ee;

                    # Partis politiques
                    $f = array();
                    $f = DB::connection("mysql")->table('partis_politiques')
                    ->where('code_requete',$r->code_requete)
                    ->select(['partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                    'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',])
                    ->get();
                    $ff = json_decode(json_encode($f), true);
                    $r->partis_politiques = $ff;

                    # Presse pieces
                    $g = array();
                    $g = DB::connection("mysql")->table('presse_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',])
                    ->get();
                    $gg = json_decode(json_encode($g), true);
                    $r->presse_pieces = $gg;
                }
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "records" => "empty"
                ]);
            }
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
    }


    /**
     * Get all requests which are definitively approved.
     *
     * @return \Illuminate\Http\Response
     */
    public function GetApproved($admin_registration_number)
    {
        //
        $admin_data = DB::connection("mysql")->table('users')->where('matricule',$admin_registration_number)->first();

        if($admin_data->departement == "tous")
        {
            $records = DB::connection("mysql")->table('recording_requests')
                ->where('recording_requests.statut_requete',"approuvee")
                ->leftjoin('international_pieces','international_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('partis_politiques','partis_politiques.code_requete','=','recording_requests.code_requete')
                ->leftjoin('parti_politique_pieces','parti_politique_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('presse_pieces','presse_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('modifications','modifications.code_requete','=','recording_requests.code_requete')
                ->select([
                    'recording_requests.statut_legal','recording_requests.denomination','recording_requests.acronyme',
                    'recording_requests.reference_misp','recording_requests.reference_daic','recording_requests.arrivee_misp',
                    'recording_requests.arrivee_daic','recording_requests.date_ag','recording_requests.departement',
                    'recording_requests.commune','recording_requests.arrondissement','recording_requests.quartier',
                    'recording_requests.numero_lot','recording_requests.immeuble','recording_requests.boite_postale',
                    'recording_requests.a_telephone','recording_requests.a_email','recording_requests.premier_responsable',
                    'recording_requests.email_premier_responsable','recording_requests.objectifs','recording_requests.observations',
                    'recording_requests.statut_requete','recording_requests.matricule_admin','recording_requests.matricule_super_admin','recording_requests.code_requete',
                    'recording_requests.respect_du_modele','recording_requests.insertion_liste_de_presence',
                    'recording_requests.validite_casiers_judiciaires','recording_requests.verification_membres_presidium','recording_requests.couverture_regionale',
                    'recording_requests.date_de_creation','recording_requests.statut_association','recording_requests.created_at',
                    'recording_requests.updated_at',
                    // 'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                    'international_pieces.duree_mandat','international_pieces.nom_du_responsable_etranger','international_pieces.contact_du_responsable_etranger',
                    'partis_politiques.ideologie',
                    'modifications.etat_modification',
                    ])
                ->orderBy('recording_requests.id', 'desc')
                ->paginate(10);
                // ->get();
            if (! empty($records))
            {
                foreach ($records as $r)
                {

                    # Multiples files
                    $multiple_files = DB::connection("mysql")->table('multiple_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->select('*')->get()->groupBy('type');
                    $r->multiple_files = $multiple_files;

                    $misc_files = DB::connection("mysql")->table('misc_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->get();
                    $r->misc_files = $misc_files;

                    # Global attachments
                    $global_attachments = DB::connection("mysql")->table('global_attachments')
                            ->where('code_requete',$r->code_requete)
                            ->select(['global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                                'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                                'global_attachments.recepisse_admin','global_attachments.ancien_recepisse_admin','global_attachments.statuts',])
                            ->first();
                    $r->global_attachments = $global_attachments;

                    # Observations
                    $c = array();
                    $c = DB::connection("mysql")->table('observations')
                    ->where('code_requete',$r->code_requete)
                    ->leftjoin('statistics','statistics.id','=','observations.statistic_id')
                    ->select(['statistics.id','statistics.element',])
                    ->get();
                    $cc = json_decode(json_encode($c), true);
                    $r->observations = $cc;

                    # International pieces
                    $d = array();
                    $d = DB::connection("mysql")->table('international_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                                'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                                'international_pieces.rapport_activites','international_pieces.statuts_mere','international_pieces.reglement_interieur_mere',])
                    ->get();
                    $dd = json_decode(json_encode($d), true);
                    $r->international_pieces = $dd;

                    # Partis politiques pieces
                    $e = array();
                    $e = DB::connection("mysql")->table('parti_politique_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',])
                    ->get();
                    $ee = json_decode(json_encode($e), true);
                    $r->parti_politique_pieces = $ee;

                    # Partis politiques
                    $f = array();
                    $f = DB::connection("mysql")->table('partis_politiques')
                    ->where('code_requete',$r->code_requete)
                    ->select(['partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                    'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',])
                    ->get();
                    $ff = json_decode(json_encode($f), true);
                    $r->partis_politiques = $ff;

                    # Presse pieces
                    $g = array();
                    $g = DB::connection("mysql")->table('presse_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',])
                    ->get();
                    $gg = json_decode(json_encode($g), true);
                    $r->presse_pieces = $gg;
                }
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "records" => "empty"
                ]);
            }
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
        else
        {
            $records = DB::connection("mysql")->table('recording_requests')
                ->where([['recording_requests.statut_requete',"approuvee"],['recording_requests.departement',$admin_data->departement]])
                ->leftjoin('international_pieces','international_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('partis_politiques','partis_politiques.code_requete','=','recording_requests.code_requete')
                ->leftjoin('parti_politique_pieces','parti_politique_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('presse_pieces','presse_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('modifications','modifications.code_requete','=','recording_requests.code_requete')
                ->select([
                    'recording_requests.statut_legal','recording_requests.denomination','recording_requests.acronyme',
                    'recording_requests.reference_misp','recording_requests.reference_daic','recording_requests.arrivee_misp',
                    'recording_requests.arrivee_daic','recording_requests.date_ag','recording_requests.departement',
                    'recording_requests.commune','recording_requests.arrondissement','recording_requests.quartier',
                    'recording_requests.numero_lot','recording_requests.immeuble','recording_requests.boite_postale',
                    'recording_requests.a_telephone','recording_requests.a_email','recording_requests.premier_responsable',
                    'recording_requests.email_premier_responsable','recording_requests.objectifs','recording_requests.observations',
                    'recording_requests.statut_requete','recording_requests.matricule_admin','recording_requests.matricule_super_admin','recording_requests.code_requete',
                    'recording_requests.respect_du_modele','recording_requests.insertion_liste_de_presence',
                    'recording_requests.validite_casiers_judiciaires','recording_requests.verification_membres_presidium','recording_requests.couverture_regionale',
                    'recording_requests.date_de_creation','recording_requests.statut_association','recording_requests.created_at',
                    'recording_requests.updated_at',
                    // 'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                    'international_pieces.duree_mandat','international_pieces.nom_du_responsable_etranger','international_pieces.contact_du_responsable_etranger',
                    'partis_politiques.ideologie',
                    'modifications.etat_modification',
                    ])
                ->orderBy('recording_requests.id', 'desc')
                ->paginate(10);
                // ->get();
            if (! empty($records))
            {
                foreach ($records as $r)
                {
                    # Multiples files
                    $multiple_files = DB::connection("mysql")->table('multiple_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->select('*')->get()->groupBy('type');
                    $r->multiple_files = $multiple_files;

                    $misc_files = DB::connection("mysql")->table('misc_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->get();
                    $r->misc_files = $misc_files;

                    # Global attachments
                    $global_attachments = DB::connection("mysql")->table('global_attachments')
                            ->where('code_requete',$r->code_requete)
                            ->select(['global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                                'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                                'global_attachments.recepisse_admin','global_attachments.ancien_recepisse_admin','global_attachments.statuts',])
                            ->first();
                    $r->global_attachments = $global_attachments;

                    # Observations
                    $c = array();
                    $c = DB::connection("mysql")->table('observations')
                    ->where('code_requete',$r->code_requete)
                    ->leftjoin('statistics','statistics.id','=','observations.statistic_id')
                    ->select(['statistics.id','statistics.element',])
                    ->get();
                    $cc = json_decode(json_encode($c), true);
                    $r->observations = $cc;

                    # International pieces
                    $d = array();
                    $d = DB::connection("mysql")->table('international_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                                'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                                'international_pieces.rapport_activites','international_pieces.statuts_mere','international_pieces.reglement_interieur_mere',])
                    ->get();
                    $dd = json_decode(json_encode($d), true);
                    $r->international_pieces = $dd;

                    # Partis politiques pieces
                    $e = array();
                    $e = DB::connection("mysql")->table('parti_politique_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',])
                    ->get();
                    $ee = json_decode(json_encode($e), true);
                    $r->parti_politique_pieces = $ee;

                    # Partis politiques
                    $f = array();
                    $f = DB::connection("mysql")->table('partis_politiques')
                    ->where('code_requete',$r->code_requete)
                    ->select(['partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                    'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',])
                    ->get();
                    $ff = json_decode(json_encode($f), true);
                    $r->partis_politiques = $ff;

                    # Presse pieces
                    $g = array();
                    $g = DB::connection("mysql")->table('presse_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',])
                    ->get();
                    $gg = json_decode(json_encode($g), true);
                    $r->presse_pieces = $gg;
                }
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "records" => "empty"
                ]);
            }
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
    }

    #############################################################################""
    /**
     * Get all requests which are definitively rejected.
     *
     * @return \Illuminate\Http\Response
     */
    public function GetRejected($admin_registration_number)
    {
        //
        $admin_data = DB::connection("mysql")->table('users')->where('matricule',$admin_registration_number)->first();

        if($admin_data->departement == "tous")
        {
            $records = DB::connection("mysql")->table('recording_requests')
                ->where('recording_requests.statut_requete',"rejetee")
                ->leftjoin('international_pieces','international_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('partis_politiques','partis_politiques.code_requete','=','recording_requests.code_requete')
                ->leftjoin('parti_politique_pieces','parti_politique_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('presse_pieces','presse_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('modifications','modifications.code_requete','=','recording_requests.code_requete')
                ->select([
                    // recording_requests
                    'recording_requests.statut_legal','recording_requests.denomination','recording_requests.acronyme',
                    'recording_requests.reference_misp','recording_requests.reference_daic','recording_requests.arrivee_misp',
                    'recording_requests.arrivee_daic','recording_requests.date_ag','recording_requests.departement',
                    'recording_requests.commune','recording_requests.arrondissement','recording_requests.quartier',
                    'recording_requests.numero_lot','recording_requests.immeuble','recording_requests.boite_postale',
                    'recording_requests.a_telephone','recording_requests.a_email','recording_requests.premier_responsable',
                    'recording_requests.email_premier_responsable','recording_requests.objectifs','recording_requests.observations',
                    'recording_requests.statut_requete','recording_requests.matricule_admin','recording_requests.matricule_super_admin','recording_requests.code_requete',
                    'recording_requests.respect_du_modele','recording_requests.insertion_liste_de_presence',
                    'recording_requests.validite_casiers_judiciaires','recording_requests.verification_membres_presidium','recording_requests.couverture_regionale',
                    'recording_requests.date_de_creation','recording_requests.statut_association','recording_requests.created_at',
                    'recording_requests.updated_at',
                    // 'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                    'international_pieces.duree_mandat','international_pieces.nom_du_responsable_etranger','international_pieces.contact_du_responsable_etranger',
                    'partis_politiques.ideologie',
                    'modifications.etat_modification',
                    ])
                ->orderBy('recording_requests.id', 'desc')
                ->paginate(10);
                // ->get();
            if (! empty($records))
            {
                foreach ($records as $r)
                {
                    # Multiples files
                    $multiple_files = DB::connection("mysql")->table('multiple_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->select('*')->get()->groupBy('type');
                    $r->multiple_files = $multiple_files;

                    $misc_files = DB::connection("mysql")->table('misc_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->get();
                    $r->misc_files = $misc_files;

                    # Global attachments
                    $global_attachments = DB::connection("mysql")->table('global_attachments')
                            ->where('code_requete',$r->code_requete)
                            ->select(['global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                                'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                                'global_attachments.recepisse_admin','global_attachments.ancien_recepisse_admin','global_attachments.statuts',])
                            ->first();
                    $r->global_attachments = $global_attachments;

                    # Observations
                    $c = array();
                    $c = DB::connection("mysql")->table('observations')
                    ->where('code_requete',$r->code_requete)
                    ->leftjoin('statistics','statistics.id','=','observations.statistic_id')
                    ->select(['statistics.id','statistics.element',])
                    ->get();
                    $cc = json_decode(json_encode($c), true);
                    $r->observations = $cc;

                    # International pieces
                    $d = array();
                    $d = DB::connection("mysql")->table('international_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                                'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                                'international_pieces.rapport_activites','international_pieces.statuts_mere','international_pieces.reglement_interieur_mere',])
                    ->get();
                    $dd = json_decode(json_encode($d), true);
                    $r->international_pieces = $dd;

                    # Partis politiques pieces
                    $e = array();
                    $e = DB::connection("mysql")->table('parti_politique_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',])
                    ->get();
                    $ee = json_decode(json_encode($e), true);
                    $r->parti_politique_pieces = $ee;

                    # Partis politiques
                    $f = array();
                    $f = DB::connection("mysql")->table('partis_politiques')
                    ->where('code_requete',$r->code_requete)
                    ->select(['partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                    'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',])
                    ->get();
                    $ff = json_decode(json_encode($f), true);
                    $r->partis_politiques = $ff;

                    # Presse pieces
                    $g = array();
                    $g = DB::connection("mysql")->table('presse_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',])
                    ->get();
                    $gg = json_decode(json_encode($g), true);
                    $r->presse_pieces = $gg;
                }
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "records" => "empty"
                ]);
            }
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
        else
        {
            $records = DB::connection("mysql")->table('recording_requests')
                ->where([['recording_requests.statut_requete',"rejetee"],['recording_requests.departement',$admin_data->departement]])
                ->leftjoin('international_pieces','international_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('partis_politiques','partis_politiques.code_requete','=','recording_requests.code_requete')
                ->leftjoin('parti_politique_pieces','parti_politique_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('presse_pieces','presse_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('modifications','modifications.code_requete','=','recording_requests.code_requete')
                ->select([
                    'recording_requests.statut_legal','recording_requests.denomination','recording_requests.acronyme',
                    'recording_requests.reference_misp','recording_requests.reference_daic','recording_requests.arrivee_misp',
                    'recording_requests.arrivee_daic','recording_requests.date_ag','recording_requests.departement',
                    'recording_requests.commune','recording_requests.arrondissement','recording_requests.quartier',
                    'recording_requests.numero_lot','recording_requests.immeuble','recording_requests.boite_postale',
                    'recording_requests.a_telephone','recording_requests.a_email','recording_requests.premier_responsable',
                    'recording_requests.email_premier_responsable','recording_requests.objectifs','recording_requests.observations',
                    'recording_requests.statut_requete','recording_requests.matricule_admin','recording_requests.matricule_super_admin','recording_requests.code_requete',
                    'recording_requests.respect_du_modele','recording_requests.insertion_liste_de_presence',
                    'recording_requests.validite_casiers_judiciaires','recording_requests.verification_membres_presidium','recording_requests.couverture_regionale',
                    'recording_requests.date_de_creation','recording_requests.statut_association','recording_requests.created_at',
                    'recording_requests.updated_at',
                    // 'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                    'international_pieces.duree_mandat','international_pieces.nom_du_responsable_etranger','international_pieces.contact_du_responsable_etranger',
                    'partis_politiques.ideologie',
                    'modifications.etat_modification',
                    ])
                ->orderBy('recording_requests.id', 'desc')
                ->paginate(10);
                // ->get();
            if (! empty($records))
            {
                foreach ($records as $r)
                {
                    # Multiples files
                    $multiple_files = DB::connection("mysql")->table('multiple_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->select('*')->get()->groupBy('type');
                    $r->multiple_files = $multiple_files;

                    $misc_files = DB::connection("mysql")->table('misc_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->get();
                    $r->misc_files = $misc_files;

                    # Global attachments
                    $global_attachments = DB::connection("mysql")->table('global_attachments')
                            ->where('code_requete',$r->code_requete)
                            ->select(['global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                                'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                                'global_attachments.recepisse_admin','global_attachments.ancien_recepisse_admin','global_attachments.statuts',])
                            ->first();
                    $r->global_attachments = $global_attachments;

                    # Observations
                    $c = array();
                    $c = DB::connection("mysql")->table('observations')
                    ->where('code_requete',$r->code_requete)
                    ->leftjoin('statistics','statistics.id','=','observations.statistic_id')
                    ->select(['statistics.id','statistics.element',])
                    ->get();
                    $cc = json_decode(json_encode($c), true);
                    $r->observations = $cc;

                    # International pieces
                    $d = array();
                    $d = DB::connection("mysql")->table('international_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                                'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                                'international_pieces.rapport_activites','international_pieces.statuts_mere','international_pieces.reglement_interieur_mere',])
                    ->get();
                    $dd = json_decode(json_encode($d), true);
                    $r->international_pieces = $dd;

                    # Partis politiques pieces
                    $e = array();
                    $e = DB::connection("mysql")->table('parti_politique_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',])
                    ->get();
                    $ee = json_decode(json_encode($e), true);
                    $r->parti_politique_pieces = $ee;

                    # Partis politiques
                    $f = array();
                    $f = DB::connection("mysql")->table('partis_politiques')
                    ->where('code_requete',$r->code_requete)
                    ->select(['partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                    'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',])
                    ->get();
                    $ff = json_decode(json_encode($f), true);
                    $r->partis_politiques = $ff;

                    # Presse pieces
                    $g = array();
                    $g = DB::connection("mysql")->table('presse_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',])
                    ->get();
                    $gg = json_decode(json_encode($g), true);
                    $r->presse_pieces = $gg;
                }
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "records" => "empty"
                ]);
            }
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
    }
    ############################################################################"


    /**
     * Get all requests which are pre approved.
     *
     * @return \Illuminate\Http\Response
     */
    public function GetApproving($admin_registration_number)
    {
        //
        $admin_data = DB::connection("mysql")->table('users')->where('matricule',$admin_registration_number)->first();

        if($admin_data->departement == "tous")
        {
            $records = DB::connection("mysql")->table('recording_requests')
                ->where('recording_requests.statut_requete',"pre approuvee")
                ->leftjoin('international_pieces','international_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('partis_politiques','partis_politiques.code_requete','=','recording_requests.code_requete')
                ->leftjoin('parti_politique_pieces','parti_politique_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('presse_pieces','presse_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('modifications','modifications.code_requete','=','recording_requests.code_requete')
                ->select([
                    'recording_requests.statut_legal','recording_requests.denomination','recording_requests.acronyme',
                    'recording_requests.reference_misp','recording_requests.reference_daic','recording_requests.arrivee_misp',
                    'recording_requests.arrivee_daic','recording_requests.date_ag','recording_requests.departement',
                    'recording_requests.commune','recording_requests.arrondissement','recording_requests.quartier',
                    'recording_requests.numero_lot','recording_requests.immeuble','recording_requests.boite_postale',
                    'recording_requests.a_telephone','recording_requests.a_email','recording_requests.premier_responsable',
                    'recording_requests.email_premier_responsable','recording_requests.objectifs','recording_requests.observations',
                    'recording_requests.statut_requete','recording_requests.matricule_admin','recording_requests.matricule_super_admin','recording_requests.code_requete',
                    'recording_requests.respect_du_modele','recording_requests.insertion_liste_de_presence',
                    'recording_requests.validite_casiers_judiciaires','recording_requests.verification_membres_presidium','recording_requests.couverture_regionale',
                    'recording_requests.date_de_creation','recording_requests.statut_association','recording_requests.created_at',
                    'recording_requests.updated_at',
                    // 'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                    'international_pieces.duree_mandat','international_pieces.nom_du_responsable_etranger','international_pieces.contact_du_responsable_etranger',
                    'partis_politiques.ideologie',
                    'modifications.etat_modification',
                    ])
                ->orderBy('recording_requests.id', 'desc')
                ->paginate(10);
                // ->get();
            if (! empty($records))
            {
                foreach ($records as $r)
                {
                    # Multiples files
                    $multiple_files = DB::connection("mysql")->table('multiple_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->select('*')->get()->groupBy('type');
                    $r->multiple_files = $multiple_files;

                    $misc_files = DB::connection("mysql")->table('misc_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->get();
                    $r->misc_files = $misc_files;

                    # Global attachments
                    $global_attachments = DB::connection("mysql")->table('global_attachments')
                            ->where('code_requete',$r->code_requete)
                            ->select(['global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                                'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                                'global_attachments.recepisse_admin','global_attachments.ancien_recepisse_admin','global_attachments.statuts',])
                            ->first();
                    $r->global_attachments = $global_attachments;

                    # Observations
                    $c = array();
                    $c = DB::connection("mysql")->table('observations')
                    ->where('code_requete',$r->code_requete)
                    ->leftjoin('statistics','statistics.id','=','observations.statistic_id')
                    ->select(['statistics.id','statistics.element',])
                    ->get();
                    $cc = json_decode(json_encode($c), true);
                    $r->observations = $cc;

                    # International pieces
                    $d = array();
                    $d = DB::connection("mysql")->table('international_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                                'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                                'international_pieces.rapport_activites','international_pieces.statuts_mere','international_pieces.reglement_interieur_mere',])
                    ->get();
                    $dd = json_decode(json_encode($d), true);
                    $r->international_pieces = $dd;

                    # Partis politiques pieces
                    $e = array();
                    $e = DB::connection("mysql")->table('parti_politique_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',])
                    ->get();
                    $ee = json_decode(json_encode($e), true);
                    $r->parti_politique_pieces = $ee;

                    # Partis politiques
                    $f = array();
                    $f = DB::connection("mysql")->table('partis_politiques')
                    ->where('code_requete',$r->code_requete)
                    ->select(['partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                    'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',])
                    ->get();
                    $ff = json_decode(json_encode($f), true);
                    $r->partis_politiques = $ff;

                    # Presse pieces
                    $g = array();
                    $g = DB::connection("mysql")->table('presse_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',])
                    ->get();
                    $gg = json_decode(json_encode($g), true);
                    $r->presse_pieces = $gg;
                }
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "records" => "empty"
                ]);
            }
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
        else
        {
            $records = DB::connection("mysql")->table('recording_requests')
                ->where([['recording_requests.statut_requete',"pre approuvee"],['recording_requests.departement',$admin_data->departement]])
                ->leftjoin('international_pieces','international_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('partis_politiques','partis_politiques.code_requete','=','recording_requests.code_requete')
                ->leftjoin('parti_politique_pieces','parti_politique_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('presse_pieces','presse_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('modifications','modifications.code_requete','=','recording_requests.code_requete')
                ->select([
                    'recording_requests.statut_legal','recording_requests.denomination','recording_requests.acronyme',
                    'recording_requests.reference_misp','recording_requests.reference_daic','recording_requests.arrivee_misp',
                    'recording_requests.arrivee_daic','recording_requests.date_ag','recording_requests.departement',
                    'recording_requests.commune','recording_requests.arrondissement','recording_requests.quartier',
                    'recording_requests.numero_lot','recording_requests.immeuble','recording_requests.boite_postale',
                    'recording_requests.a_telephone','recording_requests.a_email','recording_requests.premier_responsable',
                    'recording_requests.email_premier_responsable','recording_requests.objectifs','recording_requests.observations',
                    'recording_requests.statut_requete','recording_requests.matricule_admin','recording_requests.matricule_super_admin','recording_requests.code_requete',
                    'recording_requests.respect_du_modele','recording_requests.insertion_liste_de_presence',
                    'recording_requests.validite_casiers_judiciaires','recording_requests.verification_membres_presidium','recording_requests.couverture_regionale',
                    'recording_requests.date_de_creation','recording_requests.statut_association','recording_requests.created_at',
                    'recording_requests.updated_at',
                    // 'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                    'international_pieces.duree_mandat','international_pieces.nom_du_responsable_etranger','international_pieces.contact_du_responsable_etranger',
                    'partis_politiques.ideologie',
                    'modifications.etat_modification',
                    ])
                ->orderBy('recording_requests.id', 'desc')
                ->paginate(10);
                // ->get();
            if (! empty($records))
            {
                foreach ($records as $r)
                {
                    # Multiples files
                    $multiple_files = DB::connection("mysql")->table('multiple_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->select('*')->get()->groupBy('type');
                    $r->multiple_files = $multiple_files;

                    $misc_files = DB::connection("mysql")->table('misc_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->get();
                    $r->misc_files = $misc_files;

                    # Global attachments
                    $global_attachments = DB::connection("mysql")->table('global_attachments')
                            ->where('code_requete',$r->code_requete)
                            ->select(['global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                                'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                                'global_attachments.recepisse_admin','global_attachments.ancien_recepisse_admin','global_attachments.statuts',])
                            ->first();
                    $r->global_attachments = $global_attachments;

                    # Observations
                    $c = array();
                    $c = DB::connection("mysql")->table('observations')
                    ->where('code_requete',$r->code_requete)
                    ->leftjoin('statistics','statistics.id','=','observations.statistic_id')
                    ->select(['statistics.id','statistics.element',])
                    ->get();
                    $cc = json_decode(json_encode($c), true);
                    $r->observations = $cc;

                    # International pieces
                    $d = array();
                    $d = DB::connection("mysql")->table('international_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                                'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                                'international_pieces.rapport_activites','international_pieces.statuts_mere','international_pieces.reglement_interieur_mere',])
                    ->get();
                    $dd = json_decode(json_encode($d), true);
                    $r->international_pieces = $dd;

                    # Partis politiques pieces
                    $e = array();
                    $e = DB::connection("mysql")->table('parti_politique_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',])
                    ->get();
                    $ee = json_decode(json_encode($e), true);
                    $r->parti_politique_pieces = $ee;

                    # Partis politiques
                    $f = array();
                    $f = DB::connection("mysql")->table('partis_politiques')
                    ->where('code_requete',$r->code_requete)
                    ->select(['partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                    'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',])
                    ->get();
                    $ff = json_decode(json_encode($f), true);
                    $r->partis_politiques = $ff;

                    # Presse pieces
                    $g = array();
                    $g = DB::connection("mysql")->table('presse_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',])
                    ->get();
                    $gg = json_decode(json_encode($g), true);
                    $r->presse_pieces = $gg;
                }
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "records" => "empty"
                ]);
            }
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
    }

    /**
     * Get all requests which are pre rejected.
     *
     * @return \Illuminate\Http\Response
     */
    public function GetRejecting($admin_registration_number)
    {
        //
        $admin_data = DB::connection("mysql")->table('users')->where('matricule',$admin_registration_number)->first();

        if($admin_data->departement == "tous")
        {
            $records = DB::connection("mysql")->table('recording_requests')
                ->where('recording_requests.statut_requete',"pre rejetee")
                ->leftjoin('international_pieces','international_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('partis_politiques','partis_politiques.code_requete','=','recording_requests.code_requete')
                ->leftjoin('parti_politique_pieces','parti_politique_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('presse_pieces','presse_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('modifications','modifications.code_requete','=','recording_requests.code_requete')
                ->select([
                    'recording_requests.statut_legal','recording_requests.denomination','recording_requests.acronyme',
                    'recording_requests.reference_misp','recording_requests.reference_daic','recording_requests.arrivee_misp',
                    'recording_requests.arrivee_daic','recording_requests.date_ag','recording_requests.departement',
                    'recording_requests.commune','recording_requests.arrondissement','recording_requests.quartier',
                    'recording_requests.numero_lot','recording_requests.immeuble','recording_requests.boite_postale',
                    'recording_requests.a_telephone','recording_requests.a_email','recording_requests.premier_responsable',
                    'recording_requests.email_premier_responsable','recording_requests.objectifs','recording_requests.observations',
                    'recording_requests.statut_requete','recording_requests.matricule_admin','recording_requests.matricule_super_admin','recording_requests.code_requete',
                    'recording_requests.respect_du_modele','recording_requests.insertion_liste_de_presence',
                    'recording_requests.validite_casiers_judiciaires','recording_requests.verification_membres_presidium','recording_requests.couverture_regionale',
                    'recording_requests.date_de_creation','recording_requests.statut_association','recording_requests.created_at',
                    'recording_requests.updated_at',
                    // 'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                    'international_pieces.duree_mandat','international_pieces.nom_du_responsable_etranger','international_pieces.contact_du_responsable_etranger',
                    'partis_politiques.ideologie',
                    'modifications.etat_modification',
                    ])
                ->orderBy('recording_requests.id', 'desc')
                ->paginate(10);
                // ->get();
            if (! empty($records))
            {
                foreach ($records as $r)
                {
                    # Multiples files
                    $multiple_files = DB::connection("mysql")->table('multiple_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->select('*')->get()->groupBy('type');
                    $r->multiple_files = $multiple_files;

                    $misc_files = DB::connection("mysql")->table('misc_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->get();
                    $r->misc_files = $misc_files;

                    # Global attachments
                    $global_attachments = DB::connection("mysql")->table('global_attachments')
                            ->where('code_requete',$r->code_requete)
                            ->select(['global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                                'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                                'global_attachments.recepisse_admin','global_attachments.ancien_recepisse_admin','global_attachments.statuts',])
                            ->first();
                    $r->global_attachments = $global_attachments;

                    # Observations
                    $c = array();
                    $c = DB::connection("mysql")->table('observations')
                    ->where('code_requete',$r->code_requete)
                    ->leftjoin('statistics','statistics.id','=','observations.statistic_id')
                    ->select(['statistics.id','statistics.element',])
                    ->get();
                    $cc = json_decode(json_encode($c), true);
                    $r->observations = $cc;

                    # International pieces
                    $d = array();
                    $d = DB::connection("mysql")->table('international_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                                'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                                'international_pieces.rapport_activites','international_pieces.statuts_mere','international_pieces.reglement_interieur_mere',])
                    ->get();
                    $dd = json_decode(json_encode($d), true);
                    $r->international_pieces = $dd;

                    # Partis politiques pieces
                    $e = array();
                    $e = DB::connection("mysql")->table('parti_politique_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',])
                    ->get();
                    $ee = json_decode(json_encode($e), true);
                    $r->parti_politique_pieces = $ee;

                    # Partis politiques
                    $f = array();
                    $f = DB::connection("mysql")->table('partis_politiques')
                    ->where('code_requete',$r->code_requete)
                    ->select(['partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                    'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',])
                    ->get();
                    $ff = json_decode(json_encode($f), true);
                    $r->partis_politiques = $ff;

                    # Presse pieces
                    $g = array();
                    $g = DB::connection("mysql")->table('presse_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',])
                    ->get();
                    $gg = json_decode(json_encode($g), true);
                    $r->presse_pieces = $gg;
                }
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "records" => "empty"
                ]);
            }
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
        else
        {
            $records = DB::connection("mysql")->table('recording_requests')
                ->where([['recording_requests.statut_requete',"pre rejetee"],['recording_requests.departement',$admin_data->departement]])
                ->leftjoin('international_pieces','international_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('partis_politiques','partis_politiques.code_requete','=','recording_requests.code_requete')
                ->leftjoin('parti_politique_pieces','parti_politique_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('presse_pieces','presse_pieces.code_requete','=','recording_requests.code_requete')
                ->leftjoin('modifications','modifications.code_requete','=','recording_requests.code_requete')
                ->select([
                    'recording_requests.statut_legal','recording_requests.denomination','recording_requests.acronyme',
                    'recording_requests.reference_misp','recording_requests.reference_daic','recording_requests.arrivee_misp',
                    'recording_requests.arrivee_daic','recording_requests.date_ag','recording_requests.departement',
                    'recording_requests.commune','recording_requests.arrondissement','recording_requests.quartier',
                    'recording_requests.numero_lot','recording_requests.immeuble','recording_requests.boite_postale',
                    'recording_requests.a_telephone','recording_requests.a_email','recording_requests.premier_responsable',
                    'recording_requests.email_premier_responsable','recording_requests.objectifs','recording_requests.observations',
                    'recording_requests.statut_requete','recording_requests.matricule_admin','recording_requests.matricule_super_admin','recording_requests.code_requete',
                    'recording_requests.respect_du_modele','recording_requests.insertion_liste_de_presence',
                    'recording_requests.validite_casiers_judiciaires','recording_requests.verification_membres_presidium','recording_requests.couverture_regionale',
                    'recording_requests.date_de_creation','recording_requests.statut_association','recording_requests.created_at',
                    'recording_requests.updated_at',
                    // 'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                    'international_pieces.duree_mandat','international_pieces.nom_du_responsable_etranger','international_pieces.contact_du_responsable_etranger',
                    'partis_politiques.ideologie',
                    'modifications.etat_modification',
                    ])
                ->orderBy('recording_requests.id', 'desc')
                ->paginate(10);
                // ->get();
            if (! empty($records))
            {
                foreach ($records as $r)
                {
                    # Multiples files
                    $multiple_files = DB::connection("mysql")->table('multiple_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->select('*')->get()->groupBy('type');
                    $r->multiple_files = $multiple_files;

                    $misc_files = DB::connection("mysql")->table('misc_files')
                                        ->where('code_requete',$r->code_requete)
                                        ->get();
                    $r->misc_files = $misc_files;

                    # Global attachments
                    $global_attachments = DB::connection("mysql")->table('global_attachments')
                            ->where('code_requete',$r->code_requete)
                            ->select(['global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                                'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                                'global_attachments.recepisse_admin','global_attachments.ancien_recepisse_admin','global_attachments.statuts',])
                            ->first();
                    $r->global_attachments = $global_attachments;

                    # Observations
                    $c = array();
                    $c = DB::connection("mysql")->table('observations')
                    ->where('code_requete',$r->code_requete)
                    ->leftjoin('statistics','statistics.id','=','observations.statistic_id')
                    ->select(['statistics.id','statistics.element',])
                    ->get();
                    $cc = json_decode(json_encode($c), true);
                    $r->observations = $cc;

                    # International pieces
                    $d = array();
                    $d = DB::connection("mysql")->table('international_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                                'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                                'international_pieces.rapport_activites','international_pieces.statuts_mere','international_pieces.reglement_interieur_mere',])
                    ->get();
                    $dd = json_decode(json_encode($d), true);
                    $r->international_pieces = $dd;

                    # Partis politiques pieces
                    $e = array();
                    $e = DB::connection("mysql")->table('parti_politique_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',])
                    ->get();
                    $ee = json_decode(json_encode($e), true);
                    $r->parti_politique_pieces = $ee;

                    # Partis politiques
                    $f = array();
                    $f = DB::connection("mysql")->table('partis_politiques')
                    ->where('code_requete',$r->code_requete)
                    ->select(['partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                    'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',])
                    ->get();
                    $ff = json_decode(json_encode($f), true);
                    $r->partis_politiques = $ff;

                    # Presse pieces
                    $g = array();
                    $g = DB::connection("mysql")->table('presse_pieces')
                    ->where('code_requete',$r->code_requete)
                    ->select(['presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',])
                    ->get();
                    $gg = json_decode(json_encode($g), true);
                    $r->presse_pieces = $gg;
                }
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "records" => "empty"
                ]);
            }
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function GetRecord($code) // show # Will be use by admin and people
    {
        //
        $record = DB::connection("mysql")->table('recording_requests')
                    ->where('code_requete', $code)
                    ->exists();
        if ($record)
        {
            $record = DB::connection("mysql")->table('recording_requests')
                    ->where('recording_requests.code_requete', $code)
                    ->leftjoin('international_pieces','international_pieces.code_requete','=','recording_requests.code_requete')
                    ->leftjoin('partis_politiques','partis_politiques.code_requete','=','recording_requests.code_requete')
                    ->leftjoin('modifications','modifications.code_requete','=','recording_requests.code_requete')
                    ->select([
                        'recording_requests.statut_legal','recording_requests.denomination','recording_requests.acronyme',
                        'recording_requests.reference_misp','recording_requests.reference_daic','recording_requests.arrivee_misp',
                        'recording_requests.arrivee_daic','recording_requests.date_ag','recording_requests.departement',
                        'recording_requests.commune','recording_requests.arrondissement','recording_requests.quartier',
                        'recording_requests.numero_lot','recording_requests.immeuble','recording_requests.boite_postale',
                        'recording_requests.a_telephone','recording_requests.a_email','recording_requests.premier_responsable',
                        'recording_requests.email_premier_responsable','recording_requests.objectifs','recording_requests.observations',
                        'recording_requests.statut_requete','recording_requests.matricule_admin','recording_requests.matricule_super_admin','recording_requests.code_requete',
                        'recording_requests.respect_du_modele','recording_requests.insertion_liste_de_presence',
                        'recording_requests.validite_casiers_judiciaires','recording_requests.verification_membres_presidium','recording_requests.couverture_regionale',
                        'recording_requests.date_de_creation','recording_requests.statut_association','recording_requests.created_at',
                        'recording_requests.updated_at',
                        // 'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                        'international_pieces.duree_mandat','international_pieces.nom_du_responsable_etranger','international_pieces.contact_du_responsable_etranger',
                        'partis_politiques.ideologie',
                        'modifications.etat_modification',
                    ])
                    ->first();
                    if (! empty($record))
                    {
                        # Multiples files
                        $multiple_files = DB::connection("mysql")->table('multiple_files')
                                            ->where('code_requete',$record->code_requete)
                                            ->select('*')->get()->groupBy('type');
                        $record->multiple_files = $multiple_files;

                        $misc_files = DB::connection("mysql")->table('misc_files')
                                            ->where('code_requete',$record->code_requete)
                                            ->get();
                        $record->misc_files = $misc_files;
    
                        # Global attachments
                        $global_attachments = DB::connection("mysql")->table('global_attachments')
                                ->where('code_requete',$record->code_requete)
                                ->select(['global_attachments.demande_enregistrement','global_attachments.proces_verbal','global_attachments.membres_ag',
                                    'global_attachments.reglement_interieur','global_attachments.recepisse_de_versement',
                                    'global_attachments.recepisse_admin','global_attachments.ancien_recepisse_admin','global_attachments.statuts',])
                                ->first();
                        $record->global_attachments = $global_attachments;

                        # Observations
                        $c = array();
                        $c = DB::connection("mysql")->table('observations')
                        ->where('code_requete',$record->code_requete)
                        ->leftjoin('statistics','statistics.id','=','observations.statistic_id')
                        ->select(['statistics.id','statistics.element',])
                        ->get();
                        $cc = json_decode(json_encode($c), true);
                        $record->observations = $cc;

                        # International pieces
                        $d = array();
                        $d = DB::connection("mysql")->table('international_pieces')
                        ->where('code_requete',$record->code_requete)
                        ->select(['international_pieces.mandat','international_pieces.journal','international_pieces.recepisse_de_declaration',
                                    'international_pieces.adresse_benin','international_pieces.adresse_etranger',
                                    'international_pieces.rapport_activites','international_pieces.statuts_mere','international_pieces.reglement_interieur_mere',])
                        ->get();
                        $dd = json_decode(json_encode($d), true);
                        $record->international_pieces = $dd;

                        # Partis politiques pieces
                        $e = array();
                        $e = DB::connection("mysql")->table('parti_politique_pieces')
                        ->where('code_requete',$record->code_requete)
                        ->select(['parti_politique_pieces.acte_de_naissance','parti_politique_pieces.certificat_de_nationalite',])
                        ->get();
                        $ee = json_decode(json_encode($e), true);
                        $record->parti_politique_pieces = $ee;

                        # Partis politiques
                        $f = array();
                        $f = DB::connection("mysql")->table('partis_politiques')
                        ->where('code_requete',$record->code_requete)
                        ->select(['partis_politiques.declaration','partis_politiques.liste_de_presence','partis_politiques.liste_de_createurs',
                        'partis_politiques.projets_de_societe','partis_politiques.fiche_de_description','partis_politiques.logo_et_embleme',])
                        ->get();
                        $ff = json_decode(json_encode($f), true);
                        $record->partis_politiques = $ff;

                        # Presse pieces
                        $g = array();
                        $g = DB::connection("mysql")->table('presse_pieces')
                        ->where('code_requete',$record->code_requete)
                        ->select(['presse_pieces.cv','presse_pieces.attestation_de_travail','presse_pieces.diplome',])
                        ->get();
                        $gg = json_decode(json_encode($g), true);
                        $record->presse_pieces = $gg;
                    }
                    else
                    {
                        return response()->json([
                            "status" => 1,
                            "records" => "empty"
                        ]);
                    }

            return response()->json([
                "status" => 1,
                "existence" => "YES",
                "record" => $record,
            ]);
        }
        else
        {

            return response()->json([
                "status" => 1,
                "existence" => "NO",
                "record" => "Does not exist"
            ]);
        }
    }

    /**
     * Display the found resources based on checking constraints.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function PatternChecking(Request $request)
    {
        $request->validate([
            "pattern" => "required",
            "admin_registration_number" => "required",
            "request_status" => "required"
        ]);

        $admin_data = DB::connection("mysql")->table('users')->where('matricule',$request->admin_registration_number)->first();

        if($admin_data->departement == "tous")
        {
            $records = DB::connection("mysql")->table('recording_requests')
                ->where([['recording_requests.code_requete', 'like', '%' . $request->pattern . '%'], ['recording_requests.statut_requete', $request->request_status]])
                ->orWhere([['recording_requests.denomination', 'like', '%' . $request->pattern . '%'], ['recording_requests.statut_requete', $request->request_status]])
                ->orderBy('recording_requests.id', 'desc')
                ->paginate(10);
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
        else
        {
            $records = DB::connection("mysql")->table('recording_requests')
                ->where([['recording_requests.code_requete', 'like', '%' . $request->pattern . '%'], ['recording_requests.statut_requete', $request->request_status], ['recording_requests.departement',$admin_data->departement]])
                ->orWhere([['recording_requests.denomination', 'like', '%' . $request->pattern . '%'], ['recording_requests.statut_requete', $request->request_status], ['recording_requests.departement',$admin_data->departement]])
                ->orderBy('recording_requests.id', 'desc')
                ->paginate(10);
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
    }

    /**
     * Group associations by department.
     *
     * @param  admin_registration_number
     * @return \Illuminate\Http\Response
     */
    public function GroupByDepartment($admin_registration_number)
    {
        $admin_data = DB::connection("mysql")->table('users')->where('matricule',$admin_registration_number)->first();

        if($admin_data->departement == "tous")
        {
            $records = DB::connection("mysql")->table('recording_requests')
                            ->orderBy('recording_requests.id', 'desc')
                            ->select('*')->paginate(10)->groupBy('departement');
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
        else
        {
            $records = DB::connection("mysql")->table('recording_requests')
                            ->where('recording_requests.departement',$admin_data->departement)
                            ->orderBy('recording_requests.id', 'desc')
                            ->select('*')->paginate(10)->groupBy('departement');
            
            return response()->json([
                "status" => 1,
                "records" => $records
            ]);
        }
    }

    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function CountModifications() // store
    {
        //
        $number_of_requests = DB::connection("mysql")->table('modification')
                    ->select('etat_modification', DB::raw('count(*) as total'))
                    ->groupby("etat_modification")
                    ->orderBy('modification.id', 'desc')
                    ->get();
        return response()->json([
            "number_of_requests" => $number_of_requests
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function CountRequests() // store
    {
        $progressing = DB::connection("mysql")->table('recording_requests')
                    ->where('recording_requests.statut_requete',"en cours de traitement")
                    ->count();
        $approving = DB::connection("mysql")->table('recording_requests')
                    ->where('recording_requests.statut_requete',"pre approuvee")
                    ->count();
        $approved = DB::connection("mysql")->table('recording_requests')
                    ->where('recording_requests.statut_requete',"approuvee")
                    ->count();
        $rejecting = DB::connection("mysql")->table('recording_requests')
                    ->where('recording_requests.statut_requete',"pre rejetee")
                    ->count(); 
        $rejected = DB::connection("mysql")->table('recording_requests')
                    ->where('recording_requests.statut_requete',"rejetee")
                    ->count();
        $in_progress = $progressing + $approving + $rejecting;
        return response()->json([
            "in_progress" => $in_progress,
            "progressing" => $progressing,
            "approving" => $approving,
            "approved" => $approved,
            "rejecting" => $rejecting,
            "rejected" => $rejected,
        ]);
    }

    /**
     * Get a recording request status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function GetStatus($code) // show
    {
        //
        $request = DB::connection("mysql")->table('recording_requests')->where("code_requete",$code)->exists();
        if ($request)
        {
            $request = DB::connection("mysql")->table('recording_requests')->where("code_requete",$code)->get();
            return response()->json([
                "status" => 1,
                "record" => $request
            ]);
        }
        else
        {
            return response()->json([
                "status" => 1,
                "record" => "Invalid code."
            ]);
        }
    }

    /**
     * Freeze an admin or not.
     *
     * @return \Illuminate\Http\Response
     */
    public function FreezeAssociation($request_code, $status)
    {
        $updating = DB::connection("mysql")->table('recording_requests')
        ->where('code_requete',$request_code)
        ->update([
            'statut_association' => $status,
            'updated_at' => now()
        ]);
        if ($updating)
        {
            return response()->json([
                "status" => 1,
                "updated" => "YES"
            ]);
        }
        else
        {
            return response()->json([
                "status" => 1,
                "updated" => "NO"
            ]);
        }
    }

    /**
     * Freeze an admin or not.
     *
     * @return \Illuminate\Http\Response
     */
    public function DeleteRequest($code,$asker)
    {
        $existence = DB::connection("mysql")->table('recording_requests')->where("code_requete",$code)
                    ->exists();
        $super_admin = DB::connection("mysql")->table('users')->where([["role","super admin"],["matricule",$asker]])
                    ->exists();
        if ($existence and $super_admin)
        {
            $existence = DB::connection("mysql")->table('recording_requests')->where("code_requete",$code)
                    ->first();
            $record = RecordingRequest::findOrFail($existence->id);
            $done = $record->delete();
            if ($done)
            {
                $deletion = DB::connection("mysql")->table('international_pieces')->where("code_requete",$code)
                    ->delete();
                $deletion = DB::connection("mysql")->table('modifications')->where("code_requete",$code)
                    ->delete();
                $deletion = DB::connection("mysql")->table('observations')->where("code_requete",$code)
                    ->delete();
                $deletion = DB::connection("mysql")->table('partis_politiques')->where("code_requete",$code)
                    ->delete();
                $deletion = DB::connection("mysql")->table('presse_pieces')->where("code_requete",$code)
                    ->delete();
                return response()->json([
                    "status" => 1,
                    "deleted" => "YES"
                ]);
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "deleted" => "NO"
                ]);
            }
        }
        else
        {
            return response()->json([
                "status" => 1,
                "message" => "Access denied or association does not exist."
            ]);
        }

        
    }

    /**
     * To make a recording request.
     *
     * @return \Illuminate\Http\Response
     */
    ######################## Verified
    public function MakeRequest(Request $request) // make
    {
        $request->validate([
            "identify_route" => "required"
        ]);

        if ($request->identify_route == "create_association")
        {
            $request->validate([
                "legal_status" => "required",
                "denomination" => "required",
                "department" => "required",
                "telephone" => "required",
                "email" => "required",
                "request_code" => "required"
            ]);

            $existence = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->exists();
            if ($existence)
            {
                return response()->json([
                    "status" => 1,
                    "message" => "This request code already exists."
                ]);
            }

            $denomination_existence = DB::connection("mysql")->table('recording_requests')->where("denomination",$request->denomination)->exists();
            if ($denomination_existence)
            {
                return response()->json([
                    "status" => 1,
                    "message" => "This Denomination already exists."
                ]);
            }
            $acronym_existence = DB::connection("mysql")->table('recording_requests')->where("acronyme",$request->acronym)->exists();
            if ($acronym_existence)
            {
                return response()->json([
                    "status" => 1,
                    "message" => "This Acronym already exists."
                ]);
            }
            // Create a record
            $association_status = "inactive";
            $request_status = "en cours de traitement";
            $recording = new RecordingRequest();
            $recording->statut_legal = $request->legal_status;
            $recording->denomination = $request->denomination;
            $recording->acronyme = $request->acronym;
            $recording->date_ag = $request->ag_date;
            $recording->departement = $request->department;
            $recording->commune = $request->town;
            $recording->arrondissement = $request->district;
            $recording->quartier = $request->quarter;
            $recording->numero_lot = $request->lot_number;
            $recording->immeuble = $request->building;
            $recording->boite_postale = $request->po_box;
            $recording->a_telephone = $request->telephone;
            $recording->a_email = $request->email;
            $recording->premier_responsable = $request->first_person_in_charge;
            $recording->email_premier_responsable = $request->first_person_in_charge_email;
            $recording->objectifs = $request->goals;
            $recording->statut_requete = $request_status;
            $recording->matricule_admin = $request->admin_registration_number;
            $recording->matricule_super_admin = $request->super_admin_registration_number;
            $recording->code_requete = $request->request_code;
            $recording->reference_misp = $request->reference_to_misp;
            $recording->reference_daic = $request->reference_to_daic;
            $recording->arrivee_misp = $request->arrival_to_misp;
            $recording->arrivee_daic = $request->arrival_to_daic;
            $recording->observations = $request->observations;
            $recording->respect_du_modele = $request->model_respect;
            $recording->insertion_liste_de_presence = $request->presence_list_insertion;
            $recording->validite_casiers_judiciaires = $request->criminal_records_validity;
            $recording->verification_membres_presidium = $request->presidium_members_verification;
            $recording->statut_association = $association_status;
            $recording->couverture_regionale = $request->regional_coverage;
            $recording->save();

            // Request ID 
            $request_id = DB::connection("mysql")->table('recording_requests')
                                ->where('code_requete',$request->request_code)
                                ->first();
            $request['request_id'] = $request_id->id;

            $files_insertion = self::UploadFilesForCreate($request);
            Mail::to($request->email)
                ->cc($request->first_person_in_charge_email)
                ->send(new SendingRequestCode($request));
            return response()->json([
                "status" => 1,
                "message" => "Recording request successfully saved."
            ]);
        }
        elseif ($request->identify_route == "update_association")
        {
            $updating = $request->validate([
                'statut_legal' => "required",
                'denomination' => "required",
                'code_requete' => "required",
                'a_telephone' => "required",
                'a_email' => "required",
                // 'statut_association' => "required", // == "inactive"
                // 'statut_requete' => "required", // == "en cours de traitement"
                'acronyme' => "nullable",
                'reference_misp' => "nullable",
                'reference_daic' => "nullable",
                'arrivee_misp' => "nullable",
                'arrivee_daic' => "nullable",
                'date_ag' => "nullable",
                'departement' => "required",
                'commune' => "nullable",
                'arrondissement' => "nullable",
                'quartier' => "nullable",
                'numero_lot' => "nullable",
                'immeuble' => "nullable",
                'boite_postale' => "nullable",
                'premier_responsable' => "nullable",
                'email_premier_responsable' => "nullable",
                'objectifs' => "nullable",
                'observations' => "nullable",
                'matricule_admin' => "nullable",
                'matricule_super_admin' => "nullable",
                'respect_du_modele' => "nullable",
                'insertion_liste_de_presence' => "nullable",
                'validite_casiers_judiciaires' => "nullable",
                'verification_membres_presidium' => "nullable",
                'couverture_regionale' => "nullable",
                'date_de_creation' => "nullable"
            ]);

            $existence = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->code_requete)
                        ->exists();
            if ($existence)
            {
                $existence = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->code_requete)
                        ->first();
                $k = RecordingRequest::whereId($existence->id)->update($updating);
                $status = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->code_requete)
                                ->update([
                                    'statut_requete' => "en cours de traitement",
                                    'updated_at' => now()
                                ]);
                // Request ID 
                $request['request_id'] = $existence->id;

                $files_insertion = self::UploadFilesForUpdate($request);
                Mail::to($request->a_email)
                    ->cc($request->email_premier_responsable)
                    ->send(new SendingRequestCode($request));
                return response()->json([
                "status" => 1,
                "message" => "Recording request successfully updated."
                ]);
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "message" => "Request does not exist."
                ]);
            }
        }
        
    }

    public function UploadFilesForCreate(Request $request)
    {
        $i = 0;
        # # # Global Attachments : verified
        $verbal_trial_path = "empty";
        $ag_members_path = "empty";
        $rules_of_procedure_path = "empty";
        $payment_receipt_path = "empty";
        $recording_letter_path = "empty";
        $statute_path = "empty";
        $admin_receipt_path = "empty";
        # Verbal trial
        if ($request->hasFile('verbal_trial'))
        {
            foreach ($request->file('verbal_trial') as $v)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($v->isValid())
                {
                    $filename = $request->request_code.'_proces_verbal'.'.'.$v->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $verbal_trial_path = $v->storeAs('proces_verbaux', $filename,'public');
                    // dd($verbal_trial_path);
                }
            }
            $i = 0;
        }

        # AG Members
        if ($request->hasFile('ag_members'))
        {
            foreach ($request->file('ag_members') as $ag)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($ag->isValid())
                {
                    $filename = $request->request_code.'_membres_ag'.'.'.$ag->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $ag_members_path = $ag->storeAs('membres_ag', $filename,'public');
                }
            }
            $i = 0;
        }

        # Rules of Procedure
        if ($request->hasFile('rules_of_procedure'))
        {
            foreach ($request->file('rules_of_procedure') as $rule)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($rule->isValid())
                {
                    $filename = $request->request_code.'_reglement_interieur'.'.'.$rule->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $rules_of_procedure_path = $rule->storeAs('reglements_interieurs', $filename,'public');
                }
            }
            $i = 0;
        }

        # Payment Receipt
        if ($request->hasFile('payment_receipt'))
        {
            foreach ($request->file('payment_receipt') as $payment)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($payment->isValid())
                {
                    $filename = $request->request_code.'_recepisse_de_paiement'.'.'.$payment->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $payment_receipt_path = $payment->storeAs('recepisses_de_paiement', $filename,'public');
                }
            }
            $i = 0;
        }

        # Recording Letter
        if ($request->hasFile('recording_letter'))
        {
            foreach ($request->file('recording_letter') as $recording)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($recording->isValid())
                {
                    $filename = $request->request_code.'_lettre_de_demande'.'.'.$recording->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $recording_letter_path = $recording->storeAs('lettres_de_demande', $filename,'public');
                }
            }
            $i = 0;
        }

        # Statute
        if ($request->hasFile('statute'))
        {
            foreach ($request->file('statute') as $s)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($s->isValid())
                {
                    $filename = $request->request_code.'_statuts'.'.'.$s->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $statute_path = $s->storeAs('statuts', $filename,'public');
                }
            }
            $i = 0;
        }

        # Admin Receipt
        //Not exist for creation.

        if ($recording_letter_path != "empty" or $verbal_trial_path != "empty" or $rules_of_procedure_path != "empty" or $payment_receipt_path != "empty" or $statute_path != "empty")
        {
            $global_attachments = new GlobalAttachment();
            $global_attachments->demande_enregistrement = $recording_letter_path;
            $global_attachments->proces_verbal = $verbal_trial_path;
            $global_attachments->membres_ag = $ag_members_path;
            $global_attachments->reglement_interieur = $rules_of_procedure_path;
            $global_attachments->recepisse_de_versement = $payment_receipt_path;
            $global_attachments->recepisse_admin = $admin_receipt_path;
            $global_attachments->ancien_recepisse_admin = $admin_receipt_path;
            $global_attachments->statuts = $statute_path;
            $global_attachments->code_requete = $request->request_code;
            $global_attachments->recording_request_id = $request->request_id;
            $global_attachments->save();
        }

        # # # Multiple Files
        # Criminal Records
        if ($request->hasFile('criminal_record'))
        {
            foreach ($request->file('criminal_record') as $criminal)
            {
                $criminal_record_path = "empty";
                if ($criminal->isValid())
                {
                    $filename = Str::slug(Str::of($criminal->getClientOriginalName())->beforeLast('.'), '_').$request->request_code.'_casier_judiciaire'.'.'.$criminal->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $criminal_record_path = $criminal->storeAs('casiers_judiciaires', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $criminal_record_path;
                    $multiple_file->type = "casier judiciaire";
                    $multiple_file->code_requete = $request->request_code;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Personal Identification Certificates
        if ($request->hasFile('personal_identification_certificate'))
        {
            foreach ($request->file('personal_identification_certificate') as $pic)
            {
                $pic_path = "empty";
                if ($pic->isValid())
                {
                    $filename = Str::slug(Str::of($pic->getClientOriginalName())->beforeLast('.'), '_').$request->request_code.'_cip'.'.'.$pic->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $pic_path = $pic->storeAs('CIP', $filename, 'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $pic_path;
                    $multiple_file->type = "CIP";
                    $multiple_file->code_requete = $request->request_code;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Secured Birth Certificates
        if ($request->hasFile('secured_birth_certificate'))
        {
            foreach ($request->file('secured_birth_certificate') as $sbc)
            {
                if ($sbc->isValid())
                {
                    $filename = Str::slug(Str::of($sbc->getClientOriginalName())->beforeLast('.'), '_').$request->request_code.'_acte_de_naissance_securise'.'.'.$sbc->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $sbc_path = $sbc->storeAs('actes_de_naissance_securises', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $sbc_path;
                    $multiple_file->type = "acte de naissance securise";
                    $multiple_file->code_requete = $request->request_code;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Residence certificates
        if ($request->hasFile('residence_certificate'))
        {
            foreach ($request->file('residence_certificate') as $residence)
            {
                if ($residence->isValid())
                {
                    $filename = Str::slug(Str::of($residence->getClientOriginalName())->beforeLast('.'), '_').$request->request_code.'_certificat_de_residence'.'.'.$residence->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $residence_certificate_path = $residence->storeAs('certificats_de_residence', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $residence_certificate_path;
                    $multiple_file->type = "certificat de residence";
                    $multiple_file->code_requete = $request->request_code;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Residence Permit
        if ($request->hasFile('residence_permit'))
        {
            foreach ($request->file('residence_permit') as $permit)
            {
                if ($permit->isValid())
                {
                    $filename = Str::slug(Str::of($permit->getClientOriginalName())->beforeLast('.'), '_').$request->request_code.'_carte_de_sejour'.'.'.$permit->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $residence_permit_path = $permit->storeAs('cartes_de_sejour', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $residence_permit_path;
                    $multiple_file->type = "carte de sejour";
                    $multiple_file->code_requete = $request->request_code;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Receipt for the administrative declaration of each component federal structure
        if ($request->hasFile('federal_receipt'))
        {
            foreach ($request->file('federal_receipt') as $r)
            {
                if ($r->isValid())
                {
                    $filename = Str::slug(Str::of($r->getClientOriginalName())->beforeLast('.'), '_').$request->request_code.'_federation'.'.'.$r->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $file_path = $r->storeAs('recepisses_de_declaration_federation', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $file_path;
                    $multiple_file->type = "recepisse de declaration";
                    $multiple_file->code_requete = $request->request_code;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }
        
        # Official Newspaper which published the administrative declaration receipt for each component federal structure
        if ($request->hasFile('federal_newspaper'))
        {
            foreach ($request->file('federal_newspaper') as $r)
            {
                if ($r->isValid())
                {
                    $filename = Str::slug(Str::of($r->getClientOriginalName())->beforeLast('.'), '_').$request->request_code.'_federation'.'.'.$r->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $file_path = $r->storeAs('journaux_federation', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $file_path;
                    $multiple_file->type = "journal de publication";
                    $multiple_file->code_requete = $request->request_code;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # # # Misc Files
        # Executive Body
        if ($request->hasFile('executive_body'))
        {
            foreach ($request->file('executive_body') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->request_code.'_organe_dirigeant'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $executive_body_path = $e->storeAs('organes_dirigeants', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $executive_body_path;
                    $misc_file->type = "organe dirigeant";
                    $misc_file->code_requete = $request->request_code;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Supervisory Body
        if ($request->hasFile('supervisory_body'))
        {
            foreach ($request->file('supervisory_body') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->request_code.'_organe_de_surveillance'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $supervisory_body_path = $e->storeAs('organes_de_surveillance', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $supervisory_body_path;
                    $misc_file->type = "organe de surveillance";
                    $misc_file->code_requete = $request->request_code;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Spiritual In Charge
        if ($request->hasFile('spiritual_in_charge'))
        {
            foreach ($request->file('spiritual_in_charge') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->request_code.'_pouvoir_du_charge_spirituel'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('actes_pouvoir_du_charge_spirituel', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "acte fondant le pouvoir du charge spirituel";
                    $misc_file->code_requete = $request->request_code;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Spiritual In Charge of In Charge Of Antenna 
        if ($request->hasFile('antenna_spiritual_in_charge'))
        {
            foreach ($request->file('antenna_spiritual_in_charge') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->request_code.'_pouvoir_du_charge_spirituel_antenne'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('actes_pouvoir_du_charge_spirituel_antenne', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "acte fondant le pouvoir du charge spirituel du responsable antenne";
                    $misc_file->code_requete = $request->request_code;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Installation Permit
        if ($request->hasFile('installation_permit'))
        {
            foreach ($request->file('installation_permit') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->request_code.'_autorisation_installation'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('autorisations_installation', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "autorisation d_installation";
                    $misc_file->code_requete = $request->request_code;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Deed of gift/lease agreement/agreement of sale
        if ($request->hasFile('deed_of_belonging'))
        {
            foreach ($request->file('deed_of_belonging') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->request_code.'_acte_donation_bail_vente'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('actes_donation_bail_vente', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "acte d_appartenance";
                    $misc_file->code_requete = $request->request_code;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Criminal Record of parent In Charge Of
        if ($request->hasFile('parent_criminal_record'))
        {
            foreach ($request->file('parent_criminal_record') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->request_code.'_casier_judiciaire_du_responsable_etranger'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('casiers_judiciaires_du_responsable_etranger', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "casier judiciaire du responsable etranger";
                    $misc_file->code_requete = $request->request_code;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Recommendation Letter 
        if ($request->hasFile('recommendation_letter'))
        {
            foreach ($request->file('recommendation_letter') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->request_code.'_lettre_de_recommandation'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('lettres_de_recommandation', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "lettre de recommandation";
                    $misc_file->code_requete = $request->request_code;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Deed of Ordination 
        if ($request->hasFile('deed_of_ordination'))
        {
            foreach ($request->file('deed_of_ordination') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->request_code.'_acte_d_ordination'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('actes_d_ordination', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "acte d_ordination";
                    $misc_file->code_requete = $request->request_code;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # # # International Pieces
        # Representation Mandates
        $representation_mandate_path = "empty";
        if ($request->hasFile('representation_mandate'))
        {
            foreach ($request->file('representation_mandate') as $representation)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($representation->isValid())
                {
                    $filename = $request->request_code.'_mandat_de_representation'.'.'.$representation->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $representation_mandate_path = $representation->storeAs('mandats_de_representation', $filename,'public');
                }
            }
            $i = 0;
        }

        # Newspaper
        $newspaper_path = "empty";
        if ($request->hasFile('newspaper'))
        {
            foreach ($request->file('newspaper') as $n)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($n->isValid())
                {
                    $filename = $request->request_code.'_journal'.'.'.$n->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $newspaper_path = $n->storeAs('journaux', $filename,'public');
                }
            }
            $i = 0;
        }

        # Receipt Statement
        $receipt_statement_path = "empty";
        if ($request->hasFile('receipt_statement'))
        {
            foreach ($request->file('receipt_statement') as $r)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($r->isValid())
                {
                    $filename = $request->request_code.'_recepisse_de_declaration'.'.'.$r->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $receipt_statement_path = $r->storeAs('recepisses_de_declaration', $filename,'public');
                }
            }
            $i = 0;
        }

        # Activities Reports
        $activities_report_path = "empty";
        if ($request->hasFile('activities_report'))
        {
            foreach ($request->file('activities_report') as $activities)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($activities->isValid())
                {
                    $filename = $request->request_code.'_rapport_activites'.'.'.$activities->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $activities_report_path = $activities->storeAs('rapports_activites', $filename,'public');
                }
            }
            $i = 0;
        }

        # Benin Address
        $benin_address_path = "empty";
        if ($request->hasFile('benin_address'))
        {
            foreach ($request->file('benin_address') as $benin)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($benin->isValid())
                {
                    $filename = $request->request_code.'_adresse_au_benin'.'.'.$benin->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $benin_address_path = $benin->storeAs('adresses_au_benin', $filename,'public');
                }
            }
            $i = 0;
        }

        # Outlander Address
        $outlander_address_path = "empty";
        if ($request->hasFile('outlander_address'))
        {
            foreach ($request->file('outlander_address') as $outlander)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($outlander->isValid())
                {
                    $filename = $request->request_code.'_adresse_a_etranger'.'.'.$outlander->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $outlander_address_path = $outlander->storeAs('adresses_a_etranger', $filename,'public');
                }
            }
            $i = 0;
        }

        # Parent Statute
        $parent_statute_path = "empty";
        if ($request->hasFile('parent_statute'))
        {
            foreach ($request->file('parent_statute') as $statute)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($statute->isValid())
                {
                    $filename = $request->request_code.'_statuts_de_l_association_mere'.'.'.$statute->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $parent_statute_path = $statute->storeAs('statuts_de_l_association_mere', $filename,'public');
                }
            }
            $i = 0;
        }

        # Parent Rules of Procedure
        $parent_rules_path = "empty";
        if ($request->hasFile('parent_rules'))
        {
            foreach ($request->file('parent_rules') as $rules)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($rules->isValid())
                {
                    $filename = $request->request_code.'_reglement_interieur_de_l_association_mere'.'.'.$rules->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $parent_rules_path = $statute->storeAs('_reglements_interieurs_de_l_association_mere', $filename,'public');
                }
            }
            $i = 0;
        }

        if ($parent_statute_path != "empty" or $parent_rules_path != "empty" or $representation_mandate_path != "empty" or $newspaper_path != "empty" or $receipt_statement_path != "empty" or $activities_report_path != "empty" or $benin_address_path != "empty" or $outlander_address_path != "empty")
        {
            $international_pieces = new InternationalPiece();
            if ($request->mandate_duration)
            {
                $international_pieces->duree_mandat = $request->mandate_duration;
            }
            if ($request->name_of_person_responsible_abroad)
            {
                $international_pieces->nom_du_responsable_etranger = $request->name_of_person_responsible_abroad;
            }
            if ($request->contact_of_person_responsible_abroad)
            {
                $international_pieces->contact_du_responsable_etranger = $request->contact_of_person_responsible_abroad;
            }
            $international_pieces->mandat = $representation_mandate_path;
            $international_pieces->journal = $newspaper_path;
            $international_pieces->recepisse_de_declaration = $receipt_statement_path;
            $international_pieces->rapport_activites = $activities_report_path;
            $international_pieces->adresse_benin = $benin_address_path;
            $international_pieces->adresse_etranger = $outlander_address_path;
            $international_pieces->statuts_mere = $parent_statute_path;
            $international_pieces->reglement_interieur_mere = $parent_rules_path;
            $international_pieces->code_requete = $request->request_code;
            $international_pieces->save();
        }


        # Press pieces
        // cv
        $cv_path = "empty";
        if ($request->hasFile('cv'))
        {
            foreach ($request->file('cv') as $c)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($c->isValid())
                {
                    $filename = $request->request_code.'_cv'.'.'.$c->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $cv_path = $c->storeAs('cv', $filename,'public');
                }
            }
            $i = 0;
        }

        // Work certificate
        $work_certificate_path = "empty";
        if ($request->hasFile('work_certificate'))
        {
            foreach ($request->file('work_certificate') as $work)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($work->isValid())
                {
                    $filename = $request->request_code.'_certificat_de_travail'.'.'.$work->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $work_certificate_path = $work->storeAs('certificats_de_travail', $filename,'public');
                }
            }
            $i = 0;
        }

        // Diploma
        $diploma_path = "empty";
        if ($request->hasFile('diploma'))
        {
            foreach ($request->file('diploma') as $d)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($d->isValid())
                {
                    $filename = $request->request_code.'_diplome'.'.'.$d->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $diploma_path = $d->storeAs('diplomes', $filename,'public');
                }
            }
            $i = 0;
        }

        if ($cv_path != "empty" or $work_certificate_path != "empty" or $diploma_path != "empty")
        {
            $press = new PressePiece();
            $press->cv = $cv_path;
            $press->attestation_de_travail = $work_certificate_path;
            $press->diplome = $diploma_path;
            $press->code_requete = $request->request_code;
            $press->save();
        }

        # Political party
        // declaration
        $declaration_path = "empty";
        if ($request->hasFile('declaration'))
        {
            foreach ($request->file('declaration') as $de)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($de->isValid())
                {
                    $filename = $request->request_code.'_declaration'.'.'.$de->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $declaration_path = $de->storeAs('declarations', $filename,'public');
                }
            }
            $i = 0;
        }

        // Presence list
        $presence_list_path = "empty";
        if ($request->hasFile('presence_list'))
        {
            foreach ($request->file('presence_list') as $p)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($p->isValid())
                {
                    $filename = $request->request_code.'_liste_de_presence'.'.'.$p->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $presence_list_path = $p->storeAs('listes_de_presence', $filename,'public');
                }
            }
            $i = 0;
        }

        // society_project
        $society_projects_path = "empty";
        if ($request->hasFile('society_project'))
        {
            foreach ($request->file('society_project') as $society)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($society->isValid())
                {
                    $filename = $request->request_code.'_projet_de_societe'.'.'.$society->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $society_projects_path = $society->storeAs('projets_de_societe', $filename,'public');
                }
            }
            $i = 0;
        }

        // description_sheet
        $description_sheet_path = "empty";
        if ($request->hasFile('description_sheet'))
        {
            foreach ($request->file('description_sheet') as $sheet)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($sheet->isValid())
                {
                    $filename = $request->request_code.'_fiche_de_description'.'.'.$sheet->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $description_sheet_path = $sheet->storeAs('fiches_de_description', $filename,'public');
                }
            }
            $i = 0;
        }

        // logo and emblem
        $logo_and_emblem_path = "empty";
        if ($request->hasFile('logo_and_emblem'))
        {
            foreach ($request->file('logo_and_emblem') as $le)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($le->isValid())
                {
                    $filename = $request->request_code.'_logo_et_embleme'.'.'.$le->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $logo_and_emblem_path = $le->storeAs('logos_et_emblemes', $filename,'public');
                }
            }
            $i = 0;
        }

        // ideology
        $ideology_path = "empty";
        if ($request->hasFile('ideology'))
        {
            foreach ($request->file('ideology') as $idea)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($idea->isValid())
                {
                    $filename = $request->request_code.'_ideologie'.'.'.$idea->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $ideology_path = $idea->storeAs('ideologies', $filename,'public');
                }
            }
            $i = 0;
        }
        if ($declaration_path != "empty" or $presence_list_path != "empty" or $society_projects_path != "empty" or $description_sheet_path != "empty" or $logo_and_emblem_path != "empty" or $ideology_path != "empty")
        {
            $party = new PartisPolitiques();
            $party->declaration = $declaration_path;
            $party->liste_de_presence = $presence_list_path;
            $party->projets_de_societe = $society_projects_path;
            $party->fiche_de_description = $description_sheet_path;
            $party->logo_et_embleme = $logo_and_emblem_path;
            $party->ideologie = $ideology_path;
            $party->code_requete = $request->request_code;
            $party->save();
        }

        # # # Political party pieces
        # Birth certificates
        if ($request->hasFile('birth_certificate'))
        {
            foreach ($request->file('birth_certificate') as $birth)
            {
                $birth_certificate_path = "empty";
                if ($birth->isValid())
                {
                    $filename = Str::slug(Str::of($birth->getClientOriginalName())->beforeLast('.'), '_').$request->request_code.'_acte_de_naissance'.'.'.$birth->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $birth_certificate_path = $birth->storeAs('actes_de_naissance', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $birth_certificate_path;
                    $multiple_file->type = "acte de naissance";
                    $multiple_file->code_requete = $request->request_code;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Nationality Certificates
        if ($request->hasFile('nationality_certificate'))
        {
            foreach ($request->file('nationality_certificate') as $nationality)
            {
                $nationality_certificate_path = "empty";
                if ($nationality->isValid())
                {
                    $filename = Str::slug(Str::of($nationality->getClientOriginalName())->beforeLast('.'), '_').$request->request_code.'_certificat_de_nationalite'.'.'.$nationality->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $nationality_certificate_path = $nationality->storeAs('certificats_de_nationalite', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $nationality_certificate_path;
                    $multiple_file->type = "certificat de nationalite";
                    $multiple_file->code_requete = $request->request_code;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }
        return true;
    }


    public function UploadFilesForUpdate(Request $request)
    {
        $i = 0;
        # # # Global Attachments : verified
        $existence = DB::connection("mysql")->table('global_attachments')->where("code_requete",$request->code_requete)
                            ->exists();
        if ($existence)
        {
            $existence = DB::connection("mysql")->table('global_attachments')->where("code_requete",$request->code_requete)
                ->delete();
        }
        $verbal_trial_path = "empty";
        $ag_members_path = "empty";
        $rules_of_procedure_path = "empty";
        $payment_receipt_path = "empty";
        $recording_letter_path = "empty";
        $statute_path = "empty";
        $admin_receipt_path = "empty";
        # Verbal trial
        if ($request->hasFile('verbal_trial'))
        {
            foreach ($request->file('verbal_trial') as $v)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($v->isValid())
                {
                    $filename = $request->code_requete.'_proces_verbal'.'.'.$v->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $verbal_trial_path = $v->storeAs('proces_verbaux', $filename,'public');
                    // dd($verbal_trial_path);
                }
            }
            $i = 0;
        }

        # AG Members
        if ($request->hasFile('ag_members'))
        {
            foreach ($request->file('ag_members') as $ag)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($ag->isValid())
                {
                    $filename = $request->code_requete.'_membres_ag'.'.'.$ag->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $ag_members_path = $ag->storeAs('membres_ag', $filename,'public');
                }
            }
            $i = 0;
        }

        # Rules of Procedure
        if ($request->hasFile('rules_of_procedure'))
        {
            foreach ($request->file('rules_of_procedure') as $rule)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($rule->isValid())
                {
                    $filename = $request->code_requete.'_reglement_interieur'.'.'.$rule->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $rules_of_procedure_path = $rule->storeAs('reglements_interieurs', $filename,'public');
                }
            }
            $i = 0;
        }

        # Payment Receipt
        if ($request->hasFile('payment_receipt'))
        {
            foreach ($request->file('payment_receipt') as $payment)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($payment->isValid())
                {
                    $filename = $request->code_requete.'_recepisse_de_paiement'.'.'.$payment->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $payment_receipt_path = $payment->storeAs('recepisses_de_paiement', $filename,'public');
                }
            }
            $i = 0;
        }

        # Recording Letter
        if ($request->hasFile('recording_letter'))
        {
            foreach ($request->file('recording_letter') as $recording)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($recording->isValid())
                {
                    $filename = $request->code_requete.'_lettre_de_demande'.'.'.$recording->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $recording_letter_path = $recording->storeAs('lettres_de_demande', $filename,'public');
                }
            }
            $i = 0;
        }

        # Statute
        if ($request->hasFile('statute'))
        {
            foreach ($request->file('statute') as $s)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($s->isValid())
                {
                    $filename = $request->code_requete.'_statuts'.'.'.$s->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $statute_path = $s->storeAs('statuts', $filename,'public');
                }
            }
            $i = 0;
        }

        # Old Admin Receipt
        if ($request->hasFile('old_admin_receipt'))
        {
            foreach ($request->file('old_admin_receipt') as $receipt)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($receipt->isValid())
                {
                    $filename = $request->request_code.'_ancien_recepisse_admin'.'.'.$receipt->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $admin_receipt_path = $receipt->storeAs('anciens_recepisses_admin', $filename,'public');
                }
            }
            $i = 0;
        }

        if ($admin_receipt_path != "empty" or $recording_letter_path != "empty" or $verbal_trial_path != "empty" or $rules_of_procedure_path != "empty" or $payment_receipt_path != "empty" or $statute_path != "empty")
        {
            $global_attachments = new GlobalAttachment();
            $global_attachments->demande_enregistrement = $recording_letter_path;
            $global_attachments->proces_verbal = $verbal_trial_path;
            $global_attachments->membres_ag = $ag_members_path;
            $global_attachments->reglement_interieur = $rules_of_procedure_path;
            $global_attachments->recepisse_de_versement = $payment_receipt_path;
            $global_attachments->recepisse_admin = "empty";
            $global_attachments->ancien_recepisse_admin = $admin_receipt_path;
            $global_attachments->statuts = $statute_path;
            $global_attachments->code_requete = $request->code_requete;
            $global_attachments->recording_request_id = $request->request_id;
            $global_attachments->save();
        }

        # # # Multiple Files
        $existence = DB::connection("mysql")->table('multiple_files')->where("code_requete",$request->code_requete)
                            ->exists();
        if ($existence)
        {
            $existence = DB::connection("mysql")->table('multiple_files')->where("code_requete",$request->code_requete)
            ->delete();
        }
        # Criminal Records
        if ($request->hasFile('criminal_record'))
        {
            foreach ($request->file('criminal_record') as $criminal)
            {
                $criminal_record_path = "empty";
                if ($criminal->isValid())
                {
                    $filename = Str::slug(Str::of($criminal->getClientOriginalName())->beforeLast('.'), '_').$request->code_requete.'_casier_judiciaire'.'.'.$criminal->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $criminal_record_path = $criminal->storeAs('casiers_judiciaires', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $criminal_record_path;
                    $multiple_file->type = "casier judiciaire";
                    $multiple_file->code_requete = $request->code_requete;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Personal Identification Certificates
        if ($request->hasFile('personal_identification_certificate'))
        {
            foreach ($request->file('personal_identification_certificate') as $pic)
            {
                $pic_path = "empty";
                if ($pic->isValid())
                {
                    $filename = Str::slug(Str::of($pic->getClientOriginalName())->beforeLast('.'), '_').$request->code_requete.'_cip'.'.'.$pic->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $pic_path = $pic->storeAs('CIP', $filename, 'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $pic_path;
                    $multiple_file->type = "CIP";
                    $multiple_file->code_requete = $request->code_requete;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Secured Birth Certificates
        if ($request->hasFile('secured_birth_certificate'))
        {
            foreach ($request->file('secured_birth_certificate') as $sbc)
            {
                if ($sbc->isValid())
                {
                    $filename = Str::slug(Str::of($sbc->getClientOriginalName())->beforeLast('.'), '_').$request->code_requete.'_acte_de_naissance_securise'.'.'.$sbc->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $sbc_path = $sbc->storeAs('actes_de_naissance_securises', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $sbc_path;
                    $multiple_file->type = "acte de naissance securise";
                    $multiple_file->code_requete = $request->code_requete;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Residence certificates
        if ($request->hasFile('residence_certificate'))
        {
            foreach ($request->file('residence_certificate') as $residence)
            {
                if ($residence->isValid())
                {
                    $filename = Str::slug(Str::of($residence->getClientOriginalName())->beforeLast('.'), '_').$request->code_requete.'_certificat_de_residence'.'.'.$residence->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $residence_certificate_path = $residence->storeAs('certificats_de_residence', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $residence_certificate_path;
                    $multiple_file->type = "certificat de residence";
                    $multiple_file->code_requete = $request->code_requete;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Residence Permit
        if ($request->hasFile('residence_permit'))
        {
            foreach ($request->file('residence_permit') as $permit)
            {
                if ($permit->isValid())
                {
                    $filename = Str::slug(Str::of($permit->getClientOriginalName())->beforeLast('.'), '_').$request->code_requete.'_carte_de_sejour'.'.'.$permit->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $residence_permit_path = $permit->storeAs('cartes_de_sejour', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $residence_permit_path;
                    $multiple_file->type = "carte de sejour";
                    $multiple_file->code_requete = $request->code_requete;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Receipt for the administrative declaration of each component federal structure
        if ($request->hasFile('federal_receipt'))
        {
            foreach ($request->file('federal_receipt') as $r)
            {
                if ($r->isValid())
                {
                    $filename = Str::slug(Str::of($r->getClientOriginalName())->beforeLast('.'), '_').$request->code_requete.'_federation'.'.'.$r->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $file_path = $r->storeAs('recepisses_de_declaration_federation', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $file_path;
                    $multiple_file->type = "recepisse de declaration";
                    $multiple_file->code_requete = $request->code_requete;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }
        
        # Official Newspaper which published the administrative declaration receipt for each component federal structure
        if ($request->hasFile('federal_newspaper'))
        {
            foreach ($request->file('federal_newspaper') as $r)
            {
                if ($r->isValid())
                {
                    $filename = Str::slug(Str::of($r->getClientOriginalName())->beforeLast('.'), '_').$request->code_requete.'_federation'.'.'.$r->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $file_path = $r->storeAs('journaux_federation', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $file_path;
                    $multiple_file->type = "journal de publication";
                    $multiple_file->code_requete = $request->code_requete;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # # # Misc Files
        $existence = DB::connection("mysql")->table('misc_files')->where("code_requete",$request->code_requete)
                            ->exists();
        if ($existence)
        {
            $existence = DB::connection("mysql")->table('misc_files')->where("code_requete",$request->code_requete)
                            ->delete();
        }
        # Executive Body
        if ($request->hasFile('executive_body'))
        {
            $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "organe dirigeant"]]])
                           ->exists();
            if ($existence)
            {
                $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "organe dirigeant"]]])
                    ->delete();
            }
            foreach ($request->file('executive_body') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->code_requete.'_organe_dirigeant'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $executive_body_path = $e->storeAs('organes_dirigeants', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $executive_body_path;
                    $misc_file->type = "organe dirigeant";
                    $misc_file->code_requete = $request->code_requete;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Supervisory Body
        if ($request->hasFile('supervisory_body'))
        {
            $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "organe de surveillance"]]])
                           ->exists();
            if ($existence)
            {
                $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "organe de surveillance"]]])
                    ->delete();
            }
            foreach ($request->file('supervisory_body') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->code_requete.'_organe_de_surveillance'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $supervisory_body_path = $e->storeAs('organes_de_surveillance', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $supervisory_body_path;
                    $misc_file->type = "organe de surveillance";
                    $misc_file->code_requete = $request->code_requete;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Spiritual In Charge
        if ($request->hasFile('spiritual_in_charge'))
        {
            $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "acte fondant le pouvoir du charge spirituel"]]])
                           ->exists();
            if ($existence)
            {
                $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "acte fondant le pouvoir du charge spirituel"]]])
                    ->delete();
            }
            foreach ($request->file('spiritual_in_charge') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->code_requete.'_pouvoir_du_charge_spirituel'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('actes_pouvoir_du_charge_spirituel', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "acte fondant le pouvoir du charge spirituel";
                    $misc_file->code_requete = $request->code_requete;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Spiritual In Charge of In Charge Of Antenna 
        if ($request->hasFile('antenna_spiritual_in_charge'))
        {
            $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "acte fondant le pouvoir du charge spirituel du responsable antenne"]]])
                           ->exists();
            if ($existence)
            {
                $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "acte fondant le pouvoir du charge spirituel du responsable antenne"]]])
                    ->delete();
            }
            foreach ($request->file('antenna_spiritual_in_charge') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->code_requete.'_pouvoir_du_charge_spirituel_antenne'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('actes_pouvoir_du_charge_spirituel_antenne', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "acte fondant le pouvoir du charge spirituel du responsable antenne";
                    $misc_file->code_requete = $request->code_requete;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Installation Permit
        if ($request->hasFile('installation_permit'))
        {
            $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "autorisation d_installation"]]])
                           ->exists();
            if ($existence)
            {
                $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "autorisation d_installation"]]])
                    ->delete();
            }
            foreach ($request->file('installation_permit') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->code_requete.'_autorisation_installation'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('autorisations_installation', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "autorisation d_installation";
                    $misc_file->code_requete = $request->code_requete;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Deed of gift/lease agreement/agreement of sale
        if ($request->hasFile('deed_of_belonging'))
        {
            $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "acte d_appartenance"]]])
                           ->exists();
            if ($existence)
            {
                $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "acte d_appartenance"]]])
                    ->delete();
            }
            foreach ($request->file('deed_of_belonging') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->code_requete.'_acte_donation_bail_vente'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('actes_donation_bail_vente', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "acte d_appartenance";
                    $misc_file->code_requete = $request->code_requete;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Criminal Record of parent In Charge Of
        if ($request->hasFile('parent_criminal_record'))
        {
            $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "casier judiciaire du responsable etranger"]]])
                           ->exists();
            if ($existence)
            {
                $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "casier judiciaire du responsable etranger"]]])
                    ->delete();
            }
            foreach ($request->file('parent_criminal_record') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->code_requete.'_casier_judiciaire_du_responsable_etranger'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('casiers_judiciaires_du_responsable_etranger', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "casier judiciaire du responsable etranger";
                    $misc_file->code_requete = $request->code_requete;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Recommendation Letter 
        if ($request->hasFile('recommendation_letter'))
        {
            $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "lettre de recommandation"]]])
                           ->exists();
            if ($existence)
            {
                $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "lettre de recommandation"]]])
                    ->delete();
            }
            foreach ($request->file('recommendation_letter') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->code_requete.'_lettre_de_recommandation'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('lettres_de_recommandation', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "lettre de recommandation";
                    $misc_file->code_requete = $request->code_requete;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # Deed of Ordination 
        if ($request->hasFile('deed_of_ordination'))
        {
            $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "acte d_ordination"]]])
                           ->exists();
            if ($existence)
            {
                $existence = DB::connection("mysql")->table('misc_files')->where([["code_requete",$request->code_requete, ["type", "acte d_ordination"]]])
                    ->delete();
            }
            foreach ($request->file('deed_of_ordination') as $e)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($e->isValid())
                {
                    $filename = $request->code_requete.'_acte_d_ordination'.'.'.$e->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $misc_file_path = $e->storeAs('actes_d_ordination', $filename,'public');
                    $misc_file = new MiscFile();
                    $misc_file->file = $misc_file_path;
                    $misc_file->type = "acte d_ordination";
                    $misc_file->code_requete = $request->code_requete;
                    $misc_file->recording_request_id = $request->request_id;
                    $misc_file->save();
                }
            }
            $i = 0;
        }

        # # # International Pieces
        $existence = DB::connection("mysql")->table('international_pieces')->where("code_requete",$request->code_requete)
                       ->exists();
        if ($existence)
        {
            $existence = DB::connection("mysql")->table('international_pieces')->where("code_requete",$request->code_requete)
                ->delete();
        }
        // Representation Mandates
        $representation_mandate_path = "empty";
        $representation_mandate_path = "empty";
        if ($request->hasFile('representation_mandate'))
        {
            foreach ($request->file('representation_mandate') as $representation)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($representation->isValid())
                {
                    $filename = $request->code_requete.'_mandat_de_representation'.'.'.$representation->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $representation_mandate_path = $representation->storeAs('mandats_de_representation', $filename,'public');
                }
            }
            $i = 0;
        }

        // Newspaper
        $newspaper_path = "empty";
        if ($request->hasFile('newspaper'))
        {
            foreach ($request->file('newspaper') as $n)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($n->isValid())
                {
                    $filename = $request->code_requete.'_journal'.'.'.$n->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $newspaper_path = $n->storeAs('journaux', $filename,'public');
                }
            }
            $i = 0;
        }

        // Receipt Statement
        $receipt_statement_path = "empty";
        if ($request->hasFile('receipt_statement'))
        {
            foreach ($request->file('receipt_statement') as $r)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($r->isValid())
                {
                    $filename = $request->code_requete.'_recepisse_de_declaration'.'.'.$r->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $receipt_statement_path = $r->storeAs('recepisses_de_declaration', $filename,'public');
                }
            }
            $i = 0;
        }

        // Activities Reports
        $activities_report_path = "empty";
        if ($request->hasFile('activities_report'))
        {
            foreach ($request->file('activities_report') as $activities)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($activities->isValid())
                {
                    $filename = $request->code_requete.'_rapport_activites'.'.'.$activities->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $activities_report_path = $activities->storeAs('rapports_activites', $filename,'public');
                }
            }
            $i = 0;
        }

        // Benin Address
        $benin_address_path = "empty";
        if ($request->hasFile('benin_address'))
        {
            foreach ($request->file('benin_address') as $benin)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($benin->isValid())
                {
                    $filename = $request->code_requete.'_adresse_au_benin'.'.'.$benin->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $benin_address_path = $benin->storeAs('adresses_au_benin', $filename,'public');
                }
            }
            $i = 0;
        }

        // Outlander Address
        $outlander_address_path = "empty";
        if ($request->hasFile('outlander_address'))
        {
            foreach ($request->file('outlander_address') as $outlander)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($outlander->isValid())
                {
                    $filename = $request->code_requete.'_adresse_a_etranger'.'.'.$outlander->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $outlander_address_path = $outlander->storeAs('adresses_a_etranger', $filename,'public');
                }
            }
            $i = 0;
        }

        if ($representation_mandate_path != "empty" or $newspaper_path != "empty" or $receipt_statement_path != "empty" or $activities_report_path != "empty" or $benin_address_path != "empty" or $outlander_address_path != "empty")
        {
            $international_pieces = new InternationalPiece();
            if ($request->mandate_duration)
            {
                $international_pieces->duree_mandat = $request->mandate_duration;
            }
            if ($request->name_of_person_responsible_abroad)
            {
                $international_pieces->nom_du_responsable_etranger = $request->name_of_person_responsible_abroad;
            }
            if ($request->contact_of_person_responsible_abroad)
            {
                $international_pieces->contact_du_responsable_etranger = $request->contact_of_person_responsible_abroad;
            }
            $international_pieces->mandat = $representation_mandate_path;
            $international_pieces->journal = $newspaper_path;
            $international_pieces->recepisse_de_declaration = $receipt_statement_path;
            $international_pieces->rapport_activites = $activities_report_path;
            $international_pieces->adresse_benin = $benin_address_path;
            $international_pieces->adresse_etranger = $outlander_address_path;
            $international_pieces->code_requete = $request->code_requete;
            $international_pieces->save();
        }


        # Press pieces
        $existence = DB::connection("mysql")->table('presse_pieces')->where("code_requete",$request->code_requete)
                ->exists();
        if ($existence)
        {
            $existence = DB::connection("mysql")->table('presse_pieces')->where("code_requete",$request->code_requete)
            ->delete();
        }
        // cv
        $cv_path = "empty";
        if ($request->hasFile('cv'))
        {
            foreach ($request->file('cv') as $c)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($c->isValid())
                {
                    $filename = $request->code_requete.'_cv'.'.'.$c->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $cv_path = $c->storeAs('cv', $filename,'public');
                }
            }
            $i = 0;
        }

        // Work certificate
        $work_certificate_path = "empty";
        if ($request->hasFile('work_certificate'))
        {
            foreach ($request->file('work_certificate') as $work)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($work->isValid())
                {
                    $filename = $request->code_requete.'_certificat_de_travail'.'.'.$work->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $work_certificate_path = $work->storeAs('certificats_de_travail', $filename,'public');
                }
            }
            $i = 0;
        }

        // Diploma
        $diploma_path = "empty";
        if ($request->hasFile('diploma'))
        {
            foreach ($request->file('diploma') as $d)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($d->isValid())
                {
                    $filename = $request->code_requete.'_diplome'.'.'.$d->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $diploma_path = $d->storeAs('diplomes', $filename,'public');
                }
            }
            $i = 0;
        }

        if ($cv_path != "empty" or $work_certificate_path != "empty" or $diploma_path != "empty")
        {
            $press = new PressePiece();
            $press->cv = $cv_path;
            $press->attestation_de_travail = $work_certificate_path;
            $press->diplome = $diploma_path;
            $press->code_requete = $request->code_requete;
            $press->save();
        }

        # Political party
        $existence = DB::connection("mysql")->table('partis_politiques')->where("code_requete",$request->code_requete)
                ->exists();
        if ($existence)
        {
            $existence = DB::connection("mysql")->table('partis_politiques')->where("code_requete",$request->code_requete)
            ->delete();
        }
        // declaration
        $declaration_path = "empty";
        if ($request->hasFile('declaration'))
        {
            foreach ($request->file('declaration') as $de)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($de->isValid())
                {
                    $filename = $request->code_requete.'_declaration'.'.'.$de->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $declaration_path = $de->storeAs('declarations', $filename,'public');
                }
            }
            $i = 0;
        }

        // Presence list
        $presence_list_path = "empty";
        if ($request->hasFile('presence_list'))
        {
            foreach ($request->file('presence_list') as $p)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($p->isValid())
                {
                    $filename = $request->code_requete.'_liste_de_presence'.'.'.$p->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $presence_list_path = $p->storeAs('listes_de_presence', $filename,'public');
                }
            }
            $i = 0;
        }

        // society_project
        $society_projects_path = "empty";
        if ($request->hasFile('society_project'))
        {
            foreach ($request->file('society_project') as $society)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($society->isValid())
                {
                    $filename = $request->code_requete.'_projet_de_societe'.'.'.$society->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $society_projects_path = $society->storeAs('projets_de_societe', $filename,'public');
                }
            }
            $i = 0;
        }

        // description_sheet
        $description_sheet_path = "empty";
        if ($request->hasFile('description_sheet'))
        {
            foreach ($request->file('description_sheet') as $sheet)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($sheet->isValid())
                {
                    $filename = $request->code_requete.'_fiche_de_description'.'.'.$sheet->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $description_sheet_path = $sheet->storeAs('fiches_de_description', $filename,'public');
                }
            }
            $i = 0;
        }

        // logo and emblem
        $logo_and_emblem_path = "empty";
        if ($request->hasFile('logo_and_emblem'))
        {
            foreach ($request->file('logo_and_emblem') as $le)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($le->isValid())
                {
                    $filename = $request->code_requete.'_logo_et_embleme'.'.'.$le->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $logo_and_emblem_path = $le->storeAs('logos_et_emblemes', $filename,'public');
                }
            }
            $i = 0;
        }

        // ideology
        $ideology_path = "empty";
        if ($request->hasFile('ideology'))
        {
            foreach ($request->file('ideology') as $idea)
            {
                $i++;
                if ($i == 2)
                {
                    $i =0;
                    break;
                }
                if ($idea->isValid())
                {
                    $filename = $request->code_requete.'_ideologie'.'.'.$idea->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $ideology_path = $idea->storeAs('ideologies', $filename,'public');
                }
            }
            $i = 0;
        }

        if ($declaration_path != "empty" or $presence_list_path != "empty" or $society_projects_path != "empty" or $description_sheet_path != "empty" or $logo_and_emblem_path != "empty" or $ideology_path != "empty")
        {
            $party = new PartisPolitiques();
            $party->declaration = $declaration_path;
            $party->liste_de_presence = $presence_list_path;
            $party->projets_de_societe = $society_projects_path;
            $party->fiche_de_description = $description_sheet_path;
            $party->logo_et_embleme = $logo_and_emblem_path;
            $party->ideologie = $ideology_path;
            $party->code_requete = $request->code_requete;
            $party->save();
        }

        # # # Political party pieces
        # Birth certificates
        if ($request->hasFile('birth_certificate'))
        {
            foreach ($request->file('birth_certificate') as $birth)
            {
                $birth_certificate_path = "empty";
                if ($birth->isValid())
                {
                    $filename = Str::slug(Str::of($birth->getClientOriginalName())->beforeLast('.'), '_').$request->code_requete.'_acte_de_naissance'.'.'.$birth->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $birth_certificate_path = $birth->storeAs('actes_de_naissance', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $birth_certificate_path;
                    $multiple_file->type = "acte de naissance";
                    $multiple_file->code_requete = $request->code_requete;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        # Nationality Certificates
        if ($request->hasFile('nationality_certificate'))
        {
            foreach ($request->file('nationality_certificate') as $nationality)
            {
                $nationality_certificate_path = "empty";
                if ($nationality->isValid())
                {
                    $filename = Str::slug(Str::of($nationality->getClientOriginalName())->beforeLast('.'), '_').$request->code_requete.'_certificat_de_nationalite'.'.'.$nationality->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $nationality_certificate_path = $nationality->storeAs('certificats_de_nationalite', $filename,'public');
                    $multiple_file = new MultipleFile();
                    $multiple_file->file = $nationality_certificate_path;
                    $multiple_file->type = "certificat de nationalite";
                    $multiple_file->code_requete = $request->code_requete;
                    $multiple_file->recording_request_id = $request->request_id;
                    $multiple_file->save();
                }
            }
            $i = 0;
        }

        return true;
    }
}




// if(Mail::to($request['email'])->send(new EditMail($request)))
// {
//     $done = 1;
// }
