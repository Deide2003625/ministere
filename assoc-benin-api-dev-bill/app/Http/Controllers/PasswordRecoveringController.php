<?php

namespace App\Http\Controllers;

// use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\RecoverPassword;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PasswordRecoveringController extends Controller
{
    /**
     * Update request with admin observations.
     *
     * @return \Illuminate\Http\Response
     */
    public function Recovering(Request $request)
    {
        $request->validate([
            // "email" => 'required|unique:password_resets',
            "email" => 'required|string',
            // "email" => "required|string",
            "operation" => "required|string",
        ]);

        if ($request->operation == "recoveringLink")
        {
            $request->validate([
                "link" => "required|string",
            ]);
            $existence = DB::connection("mysql")->table('users')
                        ->where('u_email',$request->email)->exists();
            // ->where([['u_email',$request->email],['u_email',$request->email]])->first();
            if ($existence)
            {
                //create a new token to be sent to the user. 
                DB::table('password_resets')->insert([
                    'email' => $request->email,
                    'token' => Str::random(60),
                    'created_at' => now()
                    // 'created_at' => Carbon::now()
                ]);

                $tokenData = DB::table('password_resets')
                            ->where('email', $request->email)->first();

                $token = $tokenData->token;
                $email = $request->email;
                $request['token'] = $token;
                // $request['rLink'] = $request->link.$token;


                // $updated_at = (string) now();
                // $request['recorvering_password'] = $updated_at;
                // $updating = DB::connection("mysql")->table('users')
                //                 ->where('u_email',$request->email)
                //                 ->update([
                //                     'password' => Hash::make($updated_at),
                //                     'updated_at' => now()
                //                 ]);

                Mail::to($request->email)
                    ->send(new RecoverPassword($request));
            }
            return response()->json([
                "status" => 1,
                "token" => $token
            ]);
        }

        if ($request->operation == "reinitialize")
        {
            $request->validate([
                "new_password1" => 'required|string',
                "new_password2" => "required|string",
                // "password" => "required|string",
                "token" => "required|string",
            ]);
            $admin = DB::connection("mysql")->table('users')
                    ->where('u_email',$request->email)
                    ->first();
            $tokenData = DB::table('password_resets')
                        ->where([['email', $request->email],['token', $request->token]])->exists();
                        // if (Hash::check($request->password, $admin->password) and $tokenData)
            if ($tokenData)
            {
                if ($request->new_password1 == $request->new_password2)
                {
                    $updating_password = DB::connection("mysql")->table('users')
                        ->where('u_email',$request->email)
                        ->update([
                            'password' => Hash::make($request->new_password1),
                            'updated_at' => now()
                        ]);
                    DB::table('password_resets')->where('email', $request->email)->delete();
                    return response()->json([
                        "status" => 1,
                        "updated" => "YES"
                    ]);
                }
                else
                {
                    return response()->json([
                        "status" => 1,
                        "updated" => "NO : password1 != password2"
                    ]);
                }
                // Auth::login($user);
            }
            else
            {
                return response()->json([
                    "status" => 1,
                    "updated" => "Data not found."
                ]);
            }
        }
    }

    public function ShowPasswordResetForm($token)
    {
        $tokenData = DB::table('password_resets')
                        ->where('token', $token)->exists();
        if (! $tokenData)
        {
            return response()->json([
                "status" => 1,
                "access" => "NO",
            ]);
        }
        else
        {
            return response()->json([
                "status" => 1,
                "access" => "YES",
            ]);
        }
    }

    public function ResetPassword(Request $request, $token)
    {
        //some validation
   
        $password = $request->password;
        $tokenData = DB::table('password_resets')
        ->where('token', $token)->first();
   
        $user = User::where('email', $tokenData->email)->first();
        if ( !$user ) return redirect()->to('home'); //or wherever you want
   
        $user->password = Hash::make($password);
        $user->update(); //or $user->save();
   
        //do we log the user directly or let them login and try their password for the first time ? if yes 
        Auth::login($user);
   
       // If the user shouldn't reuse the token later, delete the token 
       DB::table('password_resets')->where('email', $user->email)->delete();
   
       //redirect where we want according to whether they are logged in or not.
    }
}

