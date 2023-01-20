<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Gate;
use App\Events\VehicleUpdate;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicleData = Vehicle::with('category')
                            ->select('vehicle_id','name','year','tax_date','valid_date','license_number','distance_count','vcategory_id')
                            ->get();

        return response()->json($vehicleData, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleRequest $request)
    {
        if (Gate::allows('is-superadmin') || Gate::allows('is-validator')) {
            $newData = $request->all();

            $newVehicle = Vehicle::create($newData);

            if ($newVehicle->vehicle_id != '') {
                //Broadcast to Front End Listener
                broadcast(new VehicleUpdate($newVehicle->name." Vehicle has been created"));

                return response()->json([
                    'msg' => 'Vehicle has been created',
                    'newVehicleId' => $newVehicle->vehicle_id
                ], 200);
            }

            return response()->json([
                'msg' => 'Something wrong while creating new vehicle'
            ], 500);
        } else {
            return response()->json([
                'msg' => 'Unauthorized'
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vehicleData = Vehicle::with('category')->findOrFail($id);

        return response()->json([$vehicleData], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleRequest $request, $id)
    {
        if (Gate::allows('is-superadmin') || Gate::allows('is-validator')) {
            $newData = $request->all();

            $dataUpdate = Vehicle::findOrFail($id);

            if ($dataUpdate->update($newData)) {
                //Broadcast to Front End Listener
                broadcast(new VehicleUpdate($dataUpdate->name." Vehicle has been updated"));
                
                return response()->json([
                    'msg' => 'Vehicle has been updated',
                    'updatedVehicleId' => $dataUpdate->vehicle_id
                ], 200);
            }

            return response()->json([
                'msg' => 'Something wrong while updating the vehicle'
            ], 500);
        } else {
            return response()->json([
                'msg' => 'Unauthorized'
            ], 401);
        }
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
            $deleteVehicle = Vehicle::findOrFail($id);

            if ($deleteVehicle->delete()) {
                //Broadcast to Front End Listener
                broadcast(new VehicleUpdate($deleteVehicle->name." Vehicle has been deleted"));
                
                return response()->json([
                    'msg' => 'Vehicle has been deleted'
                ], 200);
            }

            return response()->json([
                'msg' => 'There is a problem while deleting the vehicle'
            ], 500);
        } else {
            return response()->json([
                'msg' => 'Unauthorized'
            ], 401);
        }
    }
}
