<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\VehicleUsageRequest;
use App\Models\VehicleUsage;

class VehicleUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicleUsageData = VehicleUsage::select('usage_id','vehicle_id','driver_id','user_id','ucategory_id'
        ,'usage_description','personel_count','destination','start_date','end_date'
        ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
        ,'distance_count_in','status','status_description')->get();

        return response()->json($vehicleUsageData, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleUsageRequest $request)
    {
        $newData = $request->all();

        $newVehicleUsage = VehicleUsage::create($newData);

        if ($newVehicleUsage->usage_id != '') {
            return response()->json([
                'msg' => 'Vehicle usage has been created',
                'newVehicleUsageId' => $newVehicleUsage->usage_id
            ], 200);
        }

        return response()->json([
            'msg' => 'There is something wrong while creating new vehicle usage'
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
        $vehicleUsageData = VehicleUsage::findOrFail($id);

        return response()->json([$vehicleUsageData], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleUsageRequest $request, $id)
    {
        $newData = $request->all();

        $dataUpdate = VehicleUsage::findOrFail($id);

        if ($dataUpdate->update($newData)) {
            return response()->json([
                'msg' => 'Vehicle usage has been updated',
                'updatedVehicleUsageId' => $dataUpdate->usage_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while updating the vehicle usage'
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
        $deleteVehicleUsage = VehicleUsage::findOrFail($id);

        if ($deleteVehicleUsage->delete()) {
            return response()->json([
                'msg' => 'Vehicle usage has been deleted'
            ], 200);
        }

        return response()->json([
            'msg' => 'There is a problem while deleting the vehicle usage'
        ], 500);
    }
}
