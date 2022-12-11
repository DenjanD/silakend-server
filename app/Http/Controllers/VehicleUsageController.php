<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\VehicleUsageRequest;
use App\Models\VehicleUsage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class VehicleUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::allows('get-show-store-update-delete-vehicle_usages')) {
            $vehicleUsageData = VehicleUsage::select('usage_id','vehicle_id','driver_id','user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','status','status_description')->get();
        } else {
            $vehicleUsageData = VehicleUsage::select('usage_id','vehicle_id','driver_id','user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','status','status_description')
            ->where('user_id', Auth::user()->user_id)
            ->get();
        }
        
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
        if (Gate::allows('get-show-store-update-delete-vehicle_usages')) {
            $newData = $request->all();
        } else {
            $newData = $request->all();
            $newData['user_id'] = Auth::user()->user_id;
        }

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
        if (Gate::allows('get-show-store-update-delete-vehicle_usages')) {
            $vehicleUsageData = VehicleUsage::findOrFail($id);
        } else {
            $vehicleUsageData = VehicleUsage::where('user_id', Auth::user()->user_id)
                                            ->where('usage_id', $id)
                                            ->first();
        } 

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
        if (Gate::allows('get-show-store-update-delete-vehicle_usages')) {
            $newData = $request->all();

            $dataUpdate = VehicleUsage::findOrFail($id);
        } else {
            $newData = $request->all();
            $newData['user_id'] = Auth::user()->user_id;

            $dataUpdate = VehicleUsage::where('user_id', Auth::user()->user_id)
                                        ->where('usage_id', $id)
                                        ->first();
        }
        
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
        if (Gate::allows('get-show-store-update-delete-vehicle_usages')) {
            $deleteVehicleUsage = VehicleUsage::findOrFail($id);
        } else {
            $deleteVehicleUsage = VehicleUsage::where('user_id', Auth::user()->user_id)
                                            ->where('usage_id', $id)
                                            ->first();
        }

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
