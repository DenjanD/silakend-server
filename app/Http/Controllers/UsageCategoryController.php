<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UsageCategoryRequest;
use App\Models\UsageCategory;
use Illuminate\Support\Facades\Gate;
use App\Events\UsageCategoryUpdate;

class UsageCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usageCategoryData = UsageCategory::select('ucategory_id','name')
                                        ->get();

        return response()->json(
            $usageCategoryData, 200
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsageCategoryRequest $request)
    {
        if (Gate::allows('is-superadmin') || Gate::allows('is-validator')) {   
            $newData = $request->all();

            $newUsageCategory = UsageCategory::create($newData);

            if ($newUsageCategory->ucategory_id != '') {
                //Broadcast to Front End Listener
                broadcast(new UsageCategoryUpdate($newUsageCategory->name." Usage Category has been created"));


                return response()->json([
                    'msg' => 'Usage category has been created',
                    'newUsageCategoryId' => $newUsageCategory->ucategory_id
                ], 200);
            }

            return response()->json([
                'msg' => 'Something wrong while creating new usage category'
            ], 200);
        } else {
            return response()->json([
                'msg' => 'Forbidden'
            ], 403);
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
        $usageCategoryData = UsageCategory::findOrFail($id);

        return response()->json([
            $usageCategoryData
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UsageCategoryRequest $request, $id)
    {
        if (Gate::allows('is-superadmin') || Gate::allows('is-validator')) {   
            $newData = $request->all();

            $dataUpdate = UsageCategory::findOrFail($id);

            if ($dataUpdate->update($newData)) {
                //Broadcast to Front End Listener
                broadcast(new UsageCategoryUpdate($dataUpdate->name." Usage Category has been updated"));

                return response()->json([
                    'msg' => 'Usage category has been updated',
                    'updatedUsageCategoryId' => $dataUpdate->ucategory_id
                ], 200);
            }
            
            return response()->json([
                'msg' => 'Something wrong while updating user category'
            ], 500);
        } else {
            return response()->json([
                'msg' => 'Forbidden'
            ], 403);
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
            $deleteUsageCategory = UsageCategory::findOrFail($id);

            if ($deleteUsageCategory->delete()) {
                //Broadcast to Front End Listener
                broadcast(new UsageCategoryUpdate($deleteUsageCategory->name." Usage Category has been deleted"));

                return response()->json([
                    'msg' => 'Usage category has been deleted'
                ], 200);
            }

            return response()->json([
                'msg' => 'Something wrong while deleting usage category'
            ], 500);
        } else {
            return response()->json([
                'msg' => 'Forbidden'
            ], 403);
        }
    }
}
