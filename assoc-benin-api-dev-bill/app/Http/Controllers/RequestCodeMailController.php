<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SendingRequestCode;
use Illuminate\Support\Facades\Mail;

class RequestCodeMailController extends Controller
{
    /**
     * Send Request Code to association mail address and first person in charge mail address.
     *
     * @return \Illuminate\Http\Response
     */
    public function Code(Request $request)
    {
        //
        $mail = "not set";
        Mail::to($request->email)
            ->cc($request->first_person_in_charge_email)
            ->send(new SendingRequestCode($request));
        return response()->json([
            "status" => 1,
            "mail" => $mail
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
