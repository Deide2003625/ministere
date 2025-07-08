<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use Illuminate\Http\Request;
use App\Mail\RequestApproved;
use App\Mail\RequestRejected;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ObservationsManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function WritingRequest(Request $request)
    {
        //
        $request->validate([
            "number" => "nullable|integer",
            "operation" => "required|string",
        ]);
        if ($request->operation == "create")
        {
            $request->validate([
                "observation" => 'required|string',
            ]);
            $observation = new Statistic();
            $observation->element = $request->observation;
            $observation->nombre = 0;
            $observation->save();
            return response()->json([
                "insertion" => $observation
            ]);
        }
        elseif ($request->operation == "update")
        {

            $request->validate([
                "observation_id" => 'required|integer',
                "new_observation" => 'required|string',
            ]);
            $updating = DB::connection("mysql")->table('statistics')->where("id",$request->observation_id)
                            ->update([
                                'element' => $request->new_observation,
                                'updated_at' => now()
                            ]);
            return response()->json([
                "updating" => $updating
            ]);
        }
        elseif ($request->operation == "delete")
        {
            $request->validate([
                "observation_id" => 'required|integer',
            ]);
            // $deletion = DB::connection("mysql")->table('statistics')->where("element",$request->observation)
            $deletion = DB::connection("mysql")->table('statistics')->where("id",$request->observation_id)
                            ->delete();
            return response()->json([
                "deletion" => $deletion
            ]);
        }
        
        // Response
        return response()->json([
            "operation" => "Nothing is done."
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function Read()
    {
        //
        $observations = DB::connection("mysql")->table('statistics')->get();
        // Response
        return response()->json([
            "observations" => $observations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ReadOne($id)
    {
        //
        $observation = DB::connection("mysql")->table('statistics')
                    ->where('id',$id)
                    ->first();
        // Response
        return response()->json([
            "observation" => $observation
        ]);
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
