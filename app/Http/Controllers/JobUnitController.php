<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\JobUnitRequest;
use App\Models\JobUnit;
use App\Events\JobUnitUpdate;

class JobUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobUnitData = JobUnit::select('unit_id','name','unit_account')
                                ->get();
        
        return response()->json(
            $jobUnitData, 200
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JobUnitRequest $request)
    {
        $newData = $request->all();

        $newJobUnit = JobUnit::create($newData);

        if ($newJobUnit->unit_id != '') {
            //Broadcast to Front End Listener
            broadcast(new JobUnitUpdate($newJobUnit->name." Job Unit has been created"));

            return response()->json([
                'msg' => 'Job Unit has been created',
                'newJobUnitId' => $newJobUnit->unit_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while creating new job unit'
        ], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jobUnitData = JobUnit::findOrFail($id);

        return response()->json([$jobUnitData], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(JobUnitRequest $request, $id)
    {
        $newData = $request->all();

        $dataUpdate = JobUnit::findOrFail($id);

        if ($dataUpdate->update($newData)) {
            //Broadcast to Front End Listener
            broadcast(new JobUnitUpdate($dataUpdate->name." Job Unit has been updated"));

            return response()->json([
                'msg' => 'Job Unit has been updated',
                'updatedJobUnitId' => $dataUpdate->unit_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while updating the job unit'
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteJobUnit = JobUnit::findOrFail($id);

        if ($deleteJobUnit->delete()) {
            //Broadcast to Front End Listener
            broadcast(new JobUnitUpdate($deleteJobUnit->name." Job Unit has been deleted"));

            return response()->json([
                'msg' => 'Job unit has been deleted'
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while deleting the job unit'
        ], 500);
    }
}
