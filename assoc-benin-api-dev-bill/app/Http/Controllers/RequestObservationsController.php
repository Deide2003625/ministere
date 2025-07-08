<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use App\Models\Observation;
use Illuminate\Http\Request;
use App\Mail\RequestApproved;
use App\Mail\RequestRejected;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class RequestObservationsController extends Controller
{
    /**
     * Update request with admin observations.
     *
     * @return \Illuminate\Http\Response
     */
    public function AdminObservation(Request $request)
    {
            $request->validate([
                "reference_to_misp" => 'nullable|string',
                "reference_to_daic" => "nullable|string",
                // "arrival_to_misp" => 'nullable|date',
                // "arrival_to_daic" => "nullable|date",
                // "observations" => "nullable|string",
                "request_status" => "nullable|string",
                "model_respect" => "nullable|string",
                "presence_list_insertion" => "nullable|string",
                "criminal_records_validity" => "nullable|string",
                "presidium_members_verification" => "nullable|string",
                // "admin_receipt" => "nullable|string",
                "request_code" => "required",
                "admin_registration_number" => "required"
            ]);
        $record = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)->exists();
        if ($record)
        {
            $admin_data = DB::connection("mysql")->table('users')->where('matricule',$request->admin_registration_number)->first();
            if ($admin_data->role == "super admin")
            {
                $indicator = 0;
                if ($request->reference_to_misp and $request->reference_to_misp != "null")
                {
                    $insertion = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'reference_misp' => $request->reference_to_misp,
                            'matricule_super_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->reference_to_daic and $request->reference_to_daic != "null")
                {
                    $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'reference_daic' => $request->reference_to_daic,
                            'matricule_super_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->arrival_to_misp and $request->arrival_to_misp != "null")
                {
                    $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'arrivee_misp' => $request->arrival_to_misp,
                            'matricule_super_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->arrival_to_daic and $request->arrival_to_daic != "null")
                {
                    $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'arrivee_daic' => $request->arrival_to_daic,
                            'matricule_super_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->observations)
                {
                    // Peut être insérer au niveau du front.
                    $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'observations' => "oui",
                            'matricule_super_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $observations = $request->observations;
                    // $concerned_observations = array();

                    foreach ($observations as $o)
                    {
                        $select = DB::connection("mysql")->table('statistics')->where("id",$o)->first();
                        $existence = DB::connection("mysql")->table('observations')
                                    ->where([["code_requete",$request->request_code],["statistic_id",(integer)$o]])
                                    ->exists();
                        if (! $existence)
                        {
                            $observation = new Observation();
                            $observation->code_requete = $request->request_code;
                            $observation->statistic_id = (integer)$o;
                            $observation->save();
                            $updating_of_statistics = DB::connection("mysql")->table('statistics')->where("id",$o)
                            ->update([
                                'nombre' => (integer)$select->nombre + 1,
                                'updated_at' => now()
                            ]);
                        }
                        
                    }
                    $indicator += 1;
                }
                if ($request->request_status)
                {
                    $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'statut_requete' => $request->request_status,
                            'matricule_super_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->model_respect)
                {
                    $insertion = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'respect_du_modele' => $request->model_respect,
                            'matricule_super_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->presence_list_insertion)
                {
                    $insertion = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'insertion_liste_de_presence' => $request->presence_list_insertion,
                            'matricule_super_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->criminal_records_validity)
                {
                    $insertion = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'validite_casiers_judiciaires' => $request->criminal_records_validity,
                            'matricule_super_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->presidium_members_verification)
                {
                    $insertion = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'verification_membres_presidium' => $request->presidium_members_verification,
                            'matricule_super_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }

                $mail = "not set";
                $admin_receipt_path = "not set";
                if ($request->hasFile('admin_receipt') and $request->request_status == "approuvee")
                {
                    if ($request->file('admin_receipt')->isValid())
                    {
                        $filename = $request->request_code.'_admin_receipt'.'.'.$request->admin_receipt->extension();
                        if (Storage::exists($filename))
                        {
                            Storage::delete($filename);
                        }
                        $admin_receipt_path = $request->admin_receipt->storeAs('admin_receipts', $filename,'public');
                        $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                            ->update([
                                'statut_requete' => "approuvee",
                                'matricule_super_admin' => $request->admin_registration_number,
                                'statut_association' => "active",
                                'date_de_creation' => now(),
                                'updated_at' => now()
                            ]);
                        $updating = DB::connection("mysql")->table('global_attachments')->where("code_requete",$request->request_code)
                                ->update([
                                    'recepisse_admin' => $admin_receipt_path,
                                    'updated_at' => now()
                                ]);
                        if ($updating)
                        {
                            $request["admin_receipt_path"] = $admin_receipt_path;
                        }
                        $emails = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                                ->first();
                        $mail = "not set";
                        Mail::to($emails->a_email)
                            ->cc($emails->email_premier_responsable)
                            ->send(new RequestApproved($request));
                        $indicator += 1;
                    }
                }
                
                if ($indicator > 0)
                {
                    $emails = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                            ->first();
                    if ($request->request_status == "pre approuvee" or $request->request_status == "approuvee")
                    {
                        $existence = DB::connection("mysql")->table('modifications')->where("code_requete",$request->request_code)
                            ->exists();
                        if ($existence)
                        {
                            $existence = DB::connection("mysql")->table('modifications')->where("code_requete",$request->request_code)
                            ->delete();
                        }
                        ##############################################################"""
                        $existence = DB::connection("mysql")->table('observations')->where("code_requete",$request->request_code)
                            ->exists();
                        if ($existence)
                        {
                            $existence = DB::connection("mysql")->table('observations')->where("code_requete",$request->request_code)
                            ->delete();
                        }
                        ##############################################################"""
                    }
                    elseif ($request->request_status == "rejetee")
                    {
                        // $statistics = DB::connection("mysql")->table('statistics')->where("element","Nombre de rejet·s")->exists();
                        // if (! $statistics)
                        // {
                        //     $observation = new Statistic();
                        //     $observation->element = "Nombre de rejet·s";
                        //     $observation->nombre = 0;
                        //     $observation->save();
                        // }
                        // $statistics = DB::connection("mysql")->table('statistics')->where("element","Nombre de rejet·s")->first();
                        // $updating_of_number_of_rejection = DB::connection("mysql")->table('statistics')->where("element","Nombre de rejet·s")
                        //         ->update([
                        //             'nombre' => (integer)$statistics->nombre + 1,
                        //             'updated_at' => now()
                        //         ]);
                        $existence = DB::connection("mysql")->table('modifications')->where("code_requete",$request->request_code)
                            ->exists();
                        if ($existence)
                        {
                            $updating = DB::connection("mysql")->table('modifications')->where("code_requete",$request->request_code)
                                ->update([
                                    'etat_modification' => "incorrecte",
                                    'updated_at' => now()
                                ]);
                        }
                        else
                        {
                            $insertion = DB::connection("mysql")->table('modifications')
                                ->insert([
                                    'etat_modification' => "non effectuee",
                                    'code_requete' => $request->request_code,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                        }
                        #####################################################################""
                        $updating_of_status = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'statut_association' => "inactive",
                        ]);
                        ##########################################################################

                        $observations = DB::connection("mysql")->table('observations')->where("code_requete",$request->request_code)
                                        ->get('statistic_id');
                        $concerned_observations = array();

                        foreach ($observations as $o)
                        {
                            $select = DB::connection("mysql")->table('statistics')->where("id",$o->statistic_id)->first();
                            $c_observation = $select->element;
                            $concerned_observations[] = $c_observation;
                        }
                        $request->concerned_observations = $concerned_observations;

                        $mail = "not set";
                        Mail::to($emails->a_email)
                            ->cc($emails->email_premier_responsable)
                            ->send(new RequestRejected($request));
                    }
                    return response()->json([
                        "status" => 1,
                        "message" => "Record Observations successfully saved.",
                        // "mail" => $mail
                    ]);
                }
                else
                {
                    
                    return response()->json([
                        "status" => 1,
                        "message" => "There is no update."
                    ]);
                }
            }
            else
            {
                $indicator = 0;
                if ($request->reference_to_misp and $request->reference_to_misp != "null")
                {
                    $insertion = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'reference_misp' => $request->reference_to_misp,
                            'matricule_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->reference_to_daic and $request->reference_to_daic != "null")
                {
                    $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'reference_daic' => $request->reference_to_daic,
                            'matricule_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->arrival_to_misp and $request->arrival_to_misp != "null")
                {
                    $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'arrivee_misp' => $request->arrival_to_misp,
                            'matricule_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->arrival_to_daic and $request->arrival_to_daic != "null")
                {
                    $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'arrivee_daic' => $request->arrival_to_daic,
                            'matricule_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->observations)
                {
                    // Peut être insérer au niveau du front.
                    $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'observations' => "oui",
                            'matricule_super_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $observations = $request->observations;
                    // $concerned_observations = array();

                    foreach ($observations as $o)
                    {
                        $select = DB::connection("mysql")->table('statistics')->where("id",$o)->first();
                        $existence = DB::connection("mysql")->table('observations')
                                    ->where([["code_requete",$request->request_code],["statistic_id",(integer)$o]])
                                    ->exists();
                        if (! $existence)
                        {
                            $observation = new Observation();
                            $observation->code_requete = $request->request_code;
                            $observation->statistic_id = (integer)$o;
                            $observation->save();
                            $updating_of_statistics = DB::connection("mysql")->table('statistics')->where("id",$o)
                            ->update([
                                'nombre' => (integer)$select->nombre + 1,
                                'updated_at' => now()
                            ]);
                        }
                    }
                    // $request->concerned_observations = $concerned_observations;
                    $indicator += 1;
                }
                if ($request->request_status)
                {
                    $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'statut_requete' => $request->request_status,
                            'matricule_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->model_respect)
                {
                    $insertion = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'respect_du_modele' => $request->model_respect,
                            'matricule_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->presence_list_insertion)
                {
                    $insertion = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'insertion_liste_de_presence' => $request->presence_list_insertion,
                            'matricule_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->criminal_records_validity)
                {
                    $insertion = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'validite_casiers_judiciaires' => $request->criminal_records_validity,
                            'matricule_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                if ($request->presidium_members_verification)
                {
                    $insertion = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                        ->update([
                            'verification_membres_presidium' => $request->presidium_members_verification,
                            'matricule_admin' => $request->admin_registration_number,
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }

                $admin_receipt_path = "not set";
                // dd($request);
                if ($request->hasFile('admin_receipt') and $request->request_status == "approuvee")
                {
                    if ($request->file('admin_receipt')->isValid())
                    {
                        $filename = $request->request_code.'_admin_receipt'.'.'.$request->admin_receipt->extension();
                        if (Storage::exists($filename))
                        {
                            Storage::delete($filename);
                        }
                        $admin_receipt_path = $request->admin_receipt->storeAs('admin_receipts', $filename,'public');
                        $updating = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                            ->update([
                                'statut_requete' => "approuvee",
                                'matricule_admin' => $request->admin_registration_number,
                                'statut_association' => "active",
                                'date_de_creation' => now(),
                                'updated_at' => now()
                            ]);
                        
                        $updating = DB::connection("mysql")->table('global_attachments')->where("code_requete",$request->request_code)
                        ->update([
                            'recepisse_admin' => $admin_receipt_path,
                            'updated_at' => now()
                        ]);
                        if ($updating)
                        {
                            $request["admin_receipt_path"] = $admin_receipt_path;
                        }
                        $emails = DB::connection("mysql")->table('recording_requests')->where("code_requete",$request->request_code)
                                ->first();
                        $mail = "not set";
                        Mail::to($emails->a_email)
                            ->cc($emails->email_premier_responsable)
                            ->send(new RequestApproved($request));
                        $indicator += 1;
                    }
                }

                if ($indicator == 0)
                {
                    
                    return response()->json([
                        "status" => 1,
                        "message" => "There is no update."
                    ]);
                }
                else
                {
                    if ($request->request_status == "pre approuvee" or $request->request_status == "approuvee")
                    {
                        $existence = DB::connection("mysql")->table('modifications')->where("code_requete",$request->request_code)
                            ->exists();
                        if ($existence)
                        {
                            $existence = DB::connection("mysql")->table('modifications')->where("code_requete",$request->request_code)
                            ->delete();
                        }
                        ##############################################################"""
                        $existence = DB::connection("mysql")->table('observations')->where("code_requete",$request->request_code)
                            ->exists();
                        if ($existence)
                        {
                            $existence = DB::connection("mysql")->table('observations')->where("code_requete",$request->request_code)
                            ->delete();
                        }
                        ##############################################################"""
                    }
                    return response()->json([
                        "status" => 1,
                        "message" => "Saved."
                    ]);
                }
            }
        }
        else
        {
            // Response
            return response()->json([
                "status" => 0,
                "message" => "Invalid code."
            ]);
        }
    }
}
