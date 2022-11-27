<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleMaintenanceDetail;
use App\Http\Requests\VehicleMaintenanceDetailRequest;

class VehicleMaintenanceDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicleMaintenanceDetailData = VehicleMaintenanceDetail::select('detail_id','maintenance_id','item_name','item_qty','item_unit','item_price','price_total')
                                                    ->get();
        
        return response()->json($vehicleMaintenanceDetailData, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleMaintenanceDetailRequest $request)
    {
        $newData = $request->all();

        $newVehicleMaintenanceDetail = VehicleMaintenanceDetail::create($newData);

        if ($newVehicleMaintenanceDetail->detail_id != '') {
            return response()->json([
                'msg' => 'Vehicle maintenance detail has been created',
                'newVehicleMaintenanceDetailId' => $newVehicleMaintenanceDetail->detail_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while creating new vehicle maintenance detail'
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
        $vehicleMaintenanceDetailData = VehicleMaintenanceDetail::findOrFail($id);
        
        return response()->json([$vehicleMaintenanceDetailData], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleMaintenanceDetailRequest $request, $id)
    {
        $newData = $request->all();

        $dataUpdate = VehicleMaintenanceDetail::findOrFail($id);

        if ($dataUpdate->update($newData)) {
            return response()->json([
                'msg' => 'Vehicle maintenance detail has been updated',
                'updatedVehicleMaintenanceId' => $dataUpdate->detail_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while updating the vehicle maintenance detail',
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
        $deleteVehicleMaintenanceDetail = VehicleMaintenanceDetail::findOrFail($id);

        if ($deleteVehicleMaintenanceDetail->delete()) {
            return response()->json([
                'msg' => 'Vehicle maintenance detail has been deleted'
            ], 200);
        }

        return response()->json([
            'msg' => 'There is a problem while deleting the vehicle maintenance detail'
        ], 500);
    }
}
