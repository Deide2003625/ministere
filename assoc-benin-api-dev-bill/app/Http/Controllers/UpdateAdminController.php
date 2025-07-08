<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateAdminController extends Controller
{
        /**
     * Update the specified personal data.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function UpdateData(Request $request)
    {
        $request->validate([
            "u_email" => 'nullable|string|unique:users',
            "u_telephone" => "nullable|string|unique:users",
            "firstname" => "nullable|string",
            "surname" => "nullable|string",
            "registration_number" => "required"
        ]);
        $indicator = 0;
        if ($request->email)
        {
            $updating_mail = DB::connection("mysql")->table('users')
                    ->where('matricule',$request->registration_number)
                    ->update([
                        'u_email' => $request->email,
                        'updated_at' => now()
                    ]);
            $indicator += 1;
        }
        if ($request->telephone)
        {
            $updating_telephone = DB::connection("mysql")->table('users')
                    ->where('matricule',$request->registration_number)
                    ->update([
                        'u_telephone' => $request->telephone,
                        'updated_at' => now()
                    ]);
            $indicator += 1;
        }
        if ($request->firstname)
        {
            $updating_firstname = DB::connection("mysql")->table('users')
                    ->where('matricule',$request->registration_number)
                    ->update([
                        'prenom' => $request->firstname,
                        'updated_at' => now()
                    ]);
            $indicator += 1;
        }
        if ($request->surname)
        {
            $updating_surname = DB::connection("mysql")->table('users')
                    ->where('matricule',$request->registration_number)
                    ->update([
                        'nom' => $request->surname,
                        'updated_at' => now()
                    ]);
            $indicator += 1;
        }
        // $new_password2 = Hash::make($request->new_password2);
        // $last_password = Hash::make($request->last_password);
        if ($request->new_password1 and $request->new_password2 and $request->last_password)
        {
            $admin = DB::connection("mysql")->table('users')
                    ->where('matricule',$request->registration_number)
                    ->first();
            if (Hash::check($request->last_password, $admin->password))
            {
                if ($request->new_password1 == $request->new_password2)
                {
                    $updating_password = DB::connection("mysql")->table('users')
                        ->where('matricule',$request->registration_number)
                        ->update([
                            'password' => Hash::make($request->new_password1),
                            'updated_at' => now()
                        ]);
                    $indicator += 1;
                }
                else
                {
                    return response()->json([
                        "status" => 1,
                        "updated" => "NO : password 1 != password2"
                    ]);
                }
            }
        }
        
        if ($indicator > 0)
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

    
}
