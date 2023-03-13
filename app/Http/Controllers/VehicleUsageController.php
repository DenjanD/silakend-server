<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\VehicleUsageRequest;
use App\Models\VehicleUsage;
use App\Models\Vehicle;
use App\Models\User;
use App\Events\VehicleUsageUpdate;
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
            ,'distance_count_in','status','status_description')->orderBy('vehicle_usages.created_at','desc')->get();
        } else if (Gate::allows('is-verifier')) {
            $vehicleUsageData = VehicleUsage::with(['user','vehicle','driver','category'])->select('usage_id','vehicle_id','driver_id','vehicle_usages.user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','vehicle_usages.status','status_description')
            ->join('users','vehicle_usages.user_id','=','users.user_id')
            ->where('users.unit_id', Auth::user()->jobUnit->unit_id)
            ->orderBy('vehicle_usages.created_at','desc')
            ->get();
        } else if (Gate::allows('is-driver')) {
            $vehicleUsageData = VehicleUsage::with(['user','vehicle','driver','category'])->select('usage_id','vehicle_id','driver_id','vehicle_usages.user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','vehicle_usages.status','status_description')
            ->where('driver_id', Auth::user()->user_id)
            ->where('vehicle_usages.status','READY')
            ->orWhere('vehicle_usages.status','PROGRESS')
            ->orWhere('vehicle_usages.status','DONE')
            ->orderBy('vehicle_usages.created_at','desc')
            ->get();
        } else {
            $vehicleUsageData = VehicleUsage::with(['user','vehicle','driver','category'])->select('usage_id','vehicle_id','driver_id','user_id','ucategory_id'
            ,'usage_description','personel_count','destination','start_date','end_date'
            ,'depart_date','depart_time','arrive_date','arrive_time','distance_count_out'
            ,'distance_count_in','status','status_description')
            ->where('user_id', Auth::user()->user_id)
            ->orderBy('vehicle_usages.created_at','desc')
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
    public function store(Request $request)
    {
        $newData = null;
        if (Gate::allows('is-superadmin') || Gate::allows('is-validator')) {
            $newData = $request->validate([
                'vehicle_id' => 'nullable|exists:App\Models\Vehicle,vehicle_id',
                'driver_id' => 'nullable|exists:App\Models\User,user_id',
                'user_id' => 'required|exists:App\Models\User,user_id',
                'ucategory_id' => 'required|exists:App\Models\UsageCategory,ucategory_id',
                'usage_description' => 'required|string',
                'personel_count' => 'required|integer|digits_between:1,11',
                'destination' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'depart_date' => 'nullable|date',
                'depart_time' => 'nullable|date_format:H:i',
                'arrive_date' => 'nullable|date',
                'arrive_time' => 'nullable|date_format:H:i',
                'distance_count_out' => 'nullable|integer|min:0',
                'distance_count_in' => 'nullable|integer|min:0|gte:distance_count_out',
                'status' => 'required|in:WAITING,APPROVED,READY,PROGRESS,DONE,CANCELED,REJECTED',
                'status_description' => 'nullable|string'
            ]);
        } else {
            $newData = $request->validate([
                'vehicle_id' => 'prohibited',
                'driver_id' => 'prohibited',
                'user_id' => 'nullable|exists:App\Models\User,user_id',
                'ucategory_id' => 'required|exists:App\Models\UsageCategory,ucategory_id',
                'usage_description' => 'required|string',
                'personel_count' => 'required|integer|digits_between:1,11',
                'destination' => 'required|string',
                'start_date' => 'required|date|after_or_equal:'.now()->format('Y-m-d'),
                'end_date' => 'required|date|after_or_equal:start_date',
                'depart_date' => 'prohibited',
                'depart_time' => 'prohibited',
                'arrive_date' => 'prohibited',
                'arrive_time' => 'prohibited',
                'distance_count_out' => 'prohibited',
                'distance_count_in' => 'prohibited',
                'status' => 'nullable|in:WAITING,APPROVED',
                'status_description' => 'prohibited'
            ]);
            $newData['user_id'] = Auth::user()->user_id;
            if (Gate::allows('is-verifier')) {
                $newData['status'] = "APPROVED";
            } else {
                $newData['status'] = "WAITING";
            }
        } 
        $newVehicleUsage = VehicleUsage::create($newData);

        if ($newVehicleUsage->usage_id != '') {
            $userName = User::where('user_id',$newData['user_id'])->select('name')->first();

            //Broadcast to Front End Listener
            broadcast(new VehicleUsageUpdate($userName->name." telah mengajukan pengajuan peminjaman kendaraan"));

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
    public function update(Request $request, $id)
    {
        if (Gate::allows('is-superadmin')) {
            $newData = $request->all();

            $dataUpdate = VehicleUsage::findOrFail($id);
        } else if (Gate::allows('is-validator')) {
            $newData = $request->validate([
                'vehicle_id' => 'nullable|exists:App\Models\Vehicle,vehicle_id',
                'driver_id' => 'nullable|exists:App\Models\User,user_id',
                'user_id' => 'prohibited',
                'ucategory_id' => 'prohibited',
                'usage_description' => 'prohibited',
                'personel_count' => 'prohibited',
                'destination' => 'prohibited',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'depart_date' => 'prohibited',
                'depart_time' => 'prohibited',
                'arrive_date' => 'prohibited',
                'arrive_time' => 'prohibited',
                'distance_count_out' => 'prohibited',
                'distance_count_in' => 'prohibited',
                'status' => 'required|in:READY,CANCELED,REJECTED',
                'status_description' => 'nullable|string'
            ]);
            $dataUpdate = VehicleUsage::join('users','users.user_id','=','vehicle_usages.user_id')
                                        ->where('usage_id', $id)
                                        ->where('vehicle_usages.status','APPROVED')
                                        ->first();
            if ($newData['status'] == 'REJECTED') {
                $newData['vehicle_id'] = null;
                $newData['driver_id'] = null;
            }
        } else if (Gate::allows('is-verifier')) {
            $newData = $request->validate([
                'vehicle_id' => 'prohibited',
                'driver_id' => 'prohibited',
                'user_id' => 'prohibited',
                'ucategory_id' => 'required|exists:App\Models\UsageCategory,ucategory_id',
                'usage_description' => 'required|string',
                'personel_count' => 'required|integer|digits_between:1,11',
                'destination' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'depart_date' => 'prohibited',
                'depart_time' => 'prohibited',
                'arrive_date' => 'prohibited',
                'arrive_time' => 'prohibited',
                'distance_count_out' => 'prohibited',
                'distance_count_in' => 'prohibited',
                'status' => 'required|in:CANCELED,APPROVED,REJECTED',
                'status_description' => 'nullable|string'
            ]);
            $dataUpdate = VehicleUsage::join('users','users.user_id','=','vehicle_usages.user_id')
                                        ->where('usage_id', $id)
                                        ->where('users.unit_id', Auth::user()->jobUnit->unit_id)
                                        ->where('vehicle_usages.status','WAITING')
                                        ->orWhere('vehicle_usages.status','APPROVED')
                                        ->first();
        } else if (Gate::allows('is-driver')) {
            $newData = $request->validate([
                'vehicle_id' => 'prohibited',
                'driver_id' => 'prohibited',
                'user_id' => 'prohibited',
                'ucategory_id' => 'prohibited',
                'usage_description' => 'prohibited',
                'personel_count' => 'prohibited',
                'destination' => 'prohibited',
                'start_date' => 'prohibited',
                'end_date' => 'prohibited',
                'depart_date' => 'prohibited',
                'depart_time' => 'prohibited',
                'arrive_date' => 'prohibited',
                'arrive_time' => 'prohibited',
                'distance_count_out' => 'required|integer',
                'distance_count_in' => 'nullable|integer|gte:distance_count_out',
                'status' => 'required|in:PROGRESS,DONE',
                'status_description' => 'prohibited'
            ]);
            $newData['depart_date'] = Carbon::now()->format('Y-m-d');
            $newData['depart_time'] = Carbon::now()->format('H:i:m');

            $dataUpdate = VehicleUsage::where('usage_id', $id)
                                        ->where('driver_id', Auth::user()->user_id)
                                        ->where('status','READY')
                                        ->orWhere('status','PROGRESS')
                                        ->first();
            
            $validateVehicleDistance = Vehicle::where('vehicle_id',$dataUpdate->vehicle_id)->select('distance_count')->first();

            if ($newData['distance_count_out'] < $validateVehicleDistance->distance_count) {
                return response()->json([
                    'msg' => "Jumlah kilometer keluar tidak boleh kurang dari total odometer kendaraan!"
                ], 422);
            }
            
            if ($dataUpdate->depart_date != '' && $dataUpdate->depart_time != '') {
                //Update selected vehicle distance_count
                $updateVehicleDistance = Vehicle::where('vehicle_id',$dataUpdate->vehicle_id)->first();
                $totalIncrease = $newData['distance_count_in'] - $dataUpdate->distance_count_out;
                $updateVehicleDistance->distance_count += $totalIncrease;
                $updateVehicleDistance->update();

                $newData['arrive_date'] = Carbon::now()->format('Y-m-d');
                $newData['arrive_time'] = Carbon::now()->format('H:i:m');
            }
        } else {
            $newData = $request->validate([
                'vehicle_id' => 'prohibited',
                'driver_id' => 'prohibited',
                'user_id' => 'nullable|exists:App\Models\User,user_id',
                'ucategory_id' => 'required|exists:App\Models\UsageCategory,ucategory_id',
                'usage_description' => 'required|string',
                'personel_count' => 'required|integer|digits_between:1,11',
                'destination' => 'required|string',
                'start_date' => 'required|date|after_or_equal:'.now()->format('Y-m-d'),
                'end_date' => 'required|date|after_or_equal:start_date',
                'depart_date' => 'prohibited',
                'depart_time' => 'prohibited',
                'arrive_date' => 'prohibited',
                'arrive_time' => 'prohibited',
                'distance_count_out' => 'prohibited',
                'distance_count_in' => 'prohibited',
                'status' => 'nullable|in:WAITING,CANCELED',
                'status_description' => 'nullable|string'
            ]);
            $newData['user_id'] = Auth::user()->user_id;

            $dataUpdate = VehicleUsage::where('user_id', Auth::user()->user_id)
                                        ->where('usage_id', $id)
                                        ->where('status','WAITING')
                                        ->first();
        }
        
        if ($dataUpdate->update($newData)) {
            $userName = User::where('user_id',$dataUpdate->user_id)->select('name')->first();

            //Broadcast to Front End Listener
            broadcast(new VehicleUsageUpdate(Auth::user()->name." telah memperbaharui pengajuan peminjaman kendaraan"));

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
            $userName = User::where('user_id',$deleteVehicleUsage->user_id)->select('name')->first();

            //Broadcast to Front End Listener
            broadcast(new VehicleUsageUpdate($userName->name." telah membatalkan pengajuan peminjaman kendaraan"));

            return response()->json([
                'msg' => 'Vehicle usage has been deleted'
            ], 200);
        }

        return response()->json([
            'msg' => 'There is a problem while deleting the vehicle usage'
        ], 500);
    }
}
