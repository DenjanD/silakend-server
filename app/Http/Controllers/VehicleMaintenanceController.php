<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleMaintenance;
use App\Models\Vehicle;
use App\Http\Requests\VehicleMaintenanceRequest;
use App\Events\VehicleMaintenanceUpdate;

class VehicleMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicleMaintenanceData = VehicleMaintenance::with(['vehicle'])
                                                    ->select('maintenance_id','vehicle_id','date','category','description','total_cost')
                                                    ->get();
        
        return response()->json($vehicleMaintenanceData, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleMaintenanceRequest $request)
    {
        $newData = $request->all();

        $newVehicleMaintenance = VehicleMaintenance::create($newData);

        if ($newVehicleMaintenance->maintenance_id != '') {
            $vehicleName = Vehicle::where('vehicle_id',$newData['vehicle_id'])->select('name')->first();

            //Broadcast to Front End Listener
            broadcast(new VehicleMaintenanceUpdate("Vehicle Maintenance for ".$vehicleName->name." has been created"));

            return response()->json([
                'msg' => 'Vehicle maintenance has been created',
                'newVehicleMaintenanceId' => $newVehicleMaintenance->maintenance_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while creating new vehicle maintenance'
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
        $vehicleMaintenanceData = VehicleMaintenance::with(['vehicle'])
                                                    ->select('maintenance_id','vehicle_id','date','category','description','total_cost')
                                                    ->where('maintenance_id',$id)
                                                    ->get();
        
        return response()->json([$vehicleMaintenanceData], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleMaintenanceRequest $request, $id)
    {
        $newData = $request->all();

        $dataUpdate = VehicleMaintenance::findOrFail($id);

        if ($dataUpdate->update($newData)) {
            $vehicleName = Vehicle::where('vehicle_id',$newData['vehicle_id'])->select('name')->first();

            //Broadcast to Front End Listener
            broadcast(new VehicleMaintenanceUpdate("Vehicle Maintenance for ".$vehicleName->name." has been updated"));

            return response()->json([
                'msg' => 'Vehicle maintenance has been updated',
                'updatedVehicleMaintenanceId' => $dataUpdate->maintenance_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while updating the vehicle maintenance',
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
        $deleteVehicleMaintenance = VehicleMaintenance::findOrFail($id);

        if ($deleteVehicleMaintenance->delete()) {
            $vehicleName = Vehicle::where('vehicle_id',$id)->select('name')->first();

            //Broadcast to Front End Listener
            broadcast(new VehicleMaintenanceUpdate("Vehicle Maintenance for ".$vehicleName->name." has been deleted"));
            
            return response()->json([
                'msg' => 'Vehicle maintenance has been deleted'
            ], 200);
        }

        return response()->json([
            'msg' => 'There is a problem while deleting the vehicle maintenance'
        ], 500);
    }
}
