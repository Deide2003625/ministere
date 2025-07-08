<?php

namespace App\Http\Controllers;

use App\Models\PressePiece;
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

class UpdateRecordingRequestController extends Controller
{

    /**
     * Update a recording.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function UpdateRequest(Request $request) // make
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

            $files_insertion = self::UploadFiles($request);
            Mail::to($request->a_email)
                ->cc($request->email_premier_responsable)
                ->send(new SendingRequestCode($request));
            return response()->json([
            "status" => 1,
            "message" => "Recording request successfully updated."
            ]);
        }
    }

    /**
     * Store the updating record files in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function UploadFiles(Request $request)
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
        // Verbal trial
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

        // AG Members
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

        // Rules of Procedure
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

        // Payment Receipt
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

        // Recording Letter
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

        // Statute
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

        // Admin Receipt
        if ($request->hasFile('admin_receipt'))
        {
            foreach ($request->file('admin_receipt') as $receipt)
            {
                $i++;
                if ($i == 2)
                {
                    $i = 0;
                    break;
                }
                if ($receipt->isValid())
                {
                    $filename = $request->request_code.'_admin_receipt'.'.'.$receipt->extension();
                    if (Storage::exists($filename))
                    {
                        Storage::delete($filename);
                    }
                    $admin_receipt_path = $receipt->storeAs('recepisses_admin', $filename,'public');
                }
            }
            $i = 0;
        }

        if ($recording_letter_path != "empty" or $verbal_trial_path != "empty" or $rules_of_procedure_path != "empty" or $payment_receipt_path != "empty" or $statute_path != "empty")
        {
            $global_attachments = new GlobalAttachment();
            $global_attachments->demande_enregistrement = $recording_letter_path;
            $global_attachments->proces_verbal = $verbal_trial_path;
            $global_attachments->membres_ag = $ag_members_path;
            $global_attachments->reglement_interieur = $rules_of_procedure_path;
            $global_attachments->recepisse_de_versement = $payment_receipt_path;
            $global_attachments->recepisse_admin = $admin_receipt_path;
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
                    $filename = Str::slug(Str::of($criminal->getClientOriginalName())->beforeLast('.'), '_').$request->code_requete.' criminal_record'.'.'.$criminal->extension();
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

        # Residence Certificates
        if ($request->hasFile('residence_certificate'))
        {
            foreach ($request->file('residence_certificate') as $residence)
            {
                $residence_certificate_path = "empty";
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

        # International Pieces
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

        if ($representation_mandate_path != "empty" or $newspaper_path != "empty" or $receipt_statement_path != "empty" or $activities_report_path != "empty" or $benin_address_path != "empty" or $outlander_address_path != "empty")
        {
            $international_pieces = new InternationalPiece();
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
                    $i =0;
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
