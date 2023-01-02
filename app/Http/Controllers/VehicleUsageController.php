<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\VehicleUsageRequest;
use App\Models\VehicleUsage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VehicleUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::allows('is-superadmin') || Gate::allows('is-validator')) {
            $vehicleUsageData = VehicleUsage::with(['user','vehicle','driver','category'])->select('usage_id','vehicle_id','driver_id','user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','status','status_description')->get();
        } else if (Gate::allows('is-verifier')) {
            $vehicleUsageData = VehicleUsage::with(['user','vehicle','driver','category'])->select('usage_id','vehicle_id','driver_id','vehicle_usages.user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','vehicle_usages.status','status_description')
            ->join('users','vehicle_usages.user_id','=','users.user_id')
            ->where('users.unit_id', Auth::user()->jobUnit->unit_id)
            ->get();
        } else if (Gate::allows('is-driver')) {
            $vehicleUsageData = VehicleUsage::with(['user','vehicle','driver','category'])->select('usage_id','vehicle_id','driver_id','vehicle_usages.user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','vehicle_usages.status','status_description')
            ->where('driver_id', Auth::user()->user_id)
            ->get();
        } else {
            $vehicleUsageData = VehicleUsage::with(['user','vehicle','driver','category'])->select('usage_id','vehicle_id','driver_id','user_id','ucategory_id'
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
        if (Gate::allows('is-superadmin') || Gate::allows('is-validator')) {
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
        if (Gate::allows('is-superadmin') || Gate::allows('is-validator')) {
            $vehicleUsageData = VehicleUsage::with(['user','vehicle','driver','category'])->select('usage_id','vehicle_id','driver_id','vehicle_usages.user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','vehicle_usages.status','status_description')
            ->where('usage_id', $id)
            ->first();
        } else if (Gate::allows('is-verifier')) {
            $vehicleUsageData = VehicleUsage::with(['user','vehicle','driver','category'])->select('usage_id','vehicle_id','driver_id','vehicle_usages.user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','vehicle_usages.status','status_description')
            ->join('users','vehicle_usages.user_id','=','users.user_id')
            ->where('unit_id', Auth::user()->jobUnit->unit_id)
            ->where('usage_id', $id)
            ->first();
        } else if (Gate::allows('is-driver')) {
            $vehicleUsageData = VehicleUsage::with(['user','vehicle','driver','category'])->select('usage_id','vehicle_id','driver_id','vehicle_usages.user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','vehicle_usages.status','status_description')
            ->where('driver_id', Auth::user()->user_id)
            ->where('usage_id', $id)
            ->first();
        } else {
            $vehicleUsageData = VehicleUsage::with(['user','vehicle','driver','category'])->select('usage_id','vehicle_id','driver_id','vehicle_usages.user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','vehicle_usages.status','status_description')
            ->where('usage_id', $id)
            ->where('user_id', Auth::user()->user_id)
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
        if (Gate::allows('is-superadmin') || Gate::allows('is-validator')) {
            $newData = $request->all();

            $dataUpdate = VehicleUsage::findOrFail($id);
        } else if (Gate::allows('is-verifier')) {
            $newData['status'] = $request->input('status');
            $newData['status_description'] = $request->input('status_description');

            $dataUpdate = VehicleUsage::join('users','users.user_id','=','vehicle_usages.user_id')
                                        ->where('usage_id', $id)
                                        ->where('users.unit_id', Auth::user()->jobUnit->unit_id)
                                        ->first();
        } else if (Gate::allows('is-driver')) {
            $newData['status'] = $request->input('status');
            $newData['status_description'] = $request->input('status_description');
            $newData['distance_count_out'] = $request->input('distance_count_out');
            $newData['depart_date'] = Carbon::now()->format('Y-m-d');
            $newData['depart_time'] = Carbon::now()->format('H:i:m');

            $dataUpdate = VehicleUsage::where('usage_id', $id)
                                        ->where('driver_id', Auth::user()->user_id)
                                        ->first();
            
            if ($dataUpdate->depart_date != '' && $dataUpdate->depart_time != '') {
                $newData['arrive_date'] = Carbon::now()->format('Y-m-d');
                $newData['arrive_time'] = Carbon::now()->format('H:i:m');
                $newData['distance_count_in'] = $request->input('distance_count_in');
                $newData['status'] = $request->input('status');
            }
        } else {
            $newData['status'] = $request->input('status');
            $newData['status_description'] = $request->input('status_description');
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
        if (Gate::allows('is-superadmin') || Gate::allows('is-validator')) {
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
