<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\AdminRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminSigningUpController extends Controller
{
    /**
     * Add Admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function SignUp(Request $request) 
    {
        $request->validate([
            //"verbal_trial" => "required",
            "matricule" => "required|unique:users",
            "firstname" => "required",
            "surname" => "required",
            "u_telephone" => "required|unique:users",
            "u_email" => "required|unique:users",
            "role" => "required",
            "department" => "required"
        ]);

        $created_at = (string) now();
        // echo($created_at);
        // $password = Hash::make("password");
        // $password = Hash::make($created_at);
        // $request['password'] = "password";
        $request['password'] = $created_at;
        if (! $request->status)
        {
            $request['status'] = "actif";
        }

        $admin = new User();
        $admin->matricule = $request->matricule;;
        $admin->prenom = $request->firstname;;
        $admin->nom = $request->surname;;
        $admin->u_telephone = $request->u_telephone;
        $admin->u_email = $request->u_email;
        $admin->password = Hash::make($request->password);
        $admin->role = $request->role;
        $admin->departement = $request->department;
        $admin->statut = $request->status;
        $admin->save();

        // return response()->json([
        //     "status" => 1,
        //     "inserted" => "Administrateur bien configuré.",
        //     "created_at"=>$created_at // A RETIRER
        // ]);
        
        
        Mail::to($request->u_email)
            ->send(new AdminRegistration($request));
    
        return response()->json([
            "status" => 1,
            "inserted" => "Administrateur bien configuré.",
            "created_at"=>$created_at // A RETIRER
        ]);

        // return response()->json([
        //     "status" => 1,
        //     "inserted" => $password
        // ]);
        // die();
        // $insertion = DB::connection("mysql")->table('users')
        //             ->insert([
        //                 'registration_number' => $request->registration_number,
        //                 'firstname' => $request->firstname,
        //                 'surname' => $request->surname,
        //                 'telephone' => $request->telephone,
        //                 'email' => $request->email,
        //                 'password' => Hash::make($password),
        //                 'created_at' => $created_at,
        //                 'updated_at' => $created_at,
        //                 'role' => $request->role,
        //                 'status' => $request->status
        //             ]);

    }
}
