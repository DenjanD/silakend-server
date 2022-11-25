<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\VehicleCategoryRequest;
use App\Models\VehicleCategory;

class VehicleCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicleCategoryData = VehicleCategory::select('vcategory_id','name')->get();

        return response()->json($vehicleCategoryData, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleCategoryRequest $request)
    {
        $newData = $request->all();

        $newVehicleCategory = VehicleCategory::create($newData);

        if ($newVehicleCategory->vcategory_id != '') {
            return response()->json([
                'msg' => 'Vehicle category has been created',
                'newVehicleCategoryId' => $newVehicleCategory->vcategory_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while creating new vehicle category'
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
        $vehicleCategoryData = VehicleCategory::findOrFail($id);

        return response()->json([
            $vehicleCategoryData
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleCategoryRequest $request, $id)
    {
        $newData = $request->all();

        $dataUpdate = VehicleCategory::findOrFail($id);

        if ($dataUpdate->update($newData)) {
            return response()->json([
                'msg' => 'Vehicle category has been updated',
                'updatedVehicleCategoryId' => $dataUpdate->vcategory_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while updating vehicle category'
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
        $deleteVehicleCategory = VehicleCategory::findOrFail($id);

        if ($deleteVehicleCategory->delete()) {
            return response()->json([
                'msg' => 'Vehicle category has been deleted'
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while deleting vehicle category'
        ], 500);
    }
}
