<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccessController extends Controller
{
    /**
     * Verify and grant or no access to admin interface.
     *
     * @return \Illuminate\Http\Response
     */
    public function Login(Request $request)
    {
        //
        // return $request;
        // $password = Hash::make($request->password);
        /* $granted = DB::connection("mysql")->table('users')
                    ->where([["email",$request->email],["status","active"]])
                    ->first(); */
        // die($request->password);
        // $p1 = Hash::make("something");
        // if (Hash::check("something", $p1)) {
        //     die("Right");
        // }

        $granted = User::where([["u_email",$request->email],["statut","actif"]])->first() ;
        // die($granted);
        if(!empty($granted))
        {
            // die($granted->password);
            if (Hash::check($request->password, $granted->password))
            {
                $token = $granted->createToken('userToken')->plainTextToken;
                // dd($token);

                return response()->json([
                    "granted" => "YES",
                    "registration_number" => $granted->matricule,
                    "firstname" => $granted->prenom,
                    "surname" => $granted->nom,
                    "u_email" => $granted->u_email,
                    "u_telephone" => $granted->u_telephone,
                    "role" => $granted->role,
                    "status" => $granted->statut,
                    "token" => $token
                ]);
            }
        }
        else
        {
            return response()->json([
                "status" => 1,
                "granted" => "NO"
            ]);
        }
    }

    /**
     * Make an admin active or not.
     *
     * @return \Illuminate\Http\Response
     */
    public function FreezeAdmin($code, $status, $asker)
    {
        $adminAsker = DB::connection("mysql")->table('users')->where([["matricule",$asker],["role","super admin"]])->exists();
        if ($adminAsker)
        {
            $updating = DB::connection("mysql")->table('users')
            ->where('matricule',$code)
            ->update([
                'statut' => $status,
                'updated_at' => now()
            ]);

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
     * Get all admins.
     *
     * @return \Illuminate\Http\Response
     */
    public function GetAdmins()
    {
        $admins = DB::connection("mysql")->table('users')
                    ->where('role','admin')
                    ->get();

        return response()->json([
            "status" => 1,
            "admins" => $admins
        ]);
    }

    /**
     * Get all super admins.
     *
     * @return \Illuminate\Http\Response
     */
    public function GetSuperAdmins($asker)
    {
        $superAdmin = DB::connection("mysql")->table('users')->where([["matricule",$asker],["role","super admin"]])->exists();
        if ($superAdmin)
        {
            $superAdmins = DB::connection("mysql")->table('users')
                        ->where('role','super admin')
                        ->get();
            return response()->json([
                "status" => 1,
                "super admins" => $superAdmins
            ]);
        }
        else
        {
            return response()->json([
                "status" => 1,
                "super admins" => "Accès non autorisé."
            ]);
        }
    }

    

    /**
     * Make an admin active or not.
     *
     * @return \Illuminate\Http\Response
     */
    public function GetAdmin($code)
    {
        $admin = DB::connection("mysql")->table('users')
            ->where('matricule',$code)
            ->exists();
        if($admin)
        {
            $admin = DB::connection("mysql")->table('users')
                    ->where('matricule',$code)
                    ->first();
            return response()->json([
                "status" => 1,
                "admin" => $admin
            ]);
        }
        else {
            
            return response()->json([
                "status" => 1,
                "admin" => "Non récupéré."
            ]);
        }
    }


    /**
     * Make an admin active or not.
     *
     * @return \Illuminate\Http\Response
     */
    public function GetSuperAdmin($code, $asker)
    {
        $adminAsker = DB::connection("mysql")->table('users')->where([["matricule",$asker],["role","super admin"]])->exists();
        
        $superAdmin = DB::connection("mysql")->table('users')
            ->where('matricule',$code)
            ->exists();
        if($superAdmin and $adminAsker)
        {
            $superAdmin = DB::connection("mysql")->table('users')
                    ->where('matricule',$code)
                    ->first();
            return response()->json([
                "status" => 1,
                "super admin" => $superAdmin
            ]);
        }
        else {
            
            return response()->json([
                "status" => 1,
                "admin" => "Non récupéré."
            ]);
        }
    }

    public function Logout(Request $request)
    {
        // find user making request
        $user = User::find($request->user()->id);
        //delete user's tokens
        $user->tokens()->delete();

        return response([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }
}
