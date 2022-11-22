<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRoleRequest;
use App\Models\UserRole;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userRoleData = UserRole::select('user_role_id','user_id','role_id')->get();

        return response()->json($userRoleData, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRoleRequest $request)
    {
        $newData = $request->all();

        $newUserRole = UserRole::create($newData);

        if ($newUserRole->user_role_id != '') {
            return response()->json([
                'msg' => 'User role has been created',
                'newUserRoleId' => $newUserRole->user_role_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while creating new user role'
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
        $userRoleData = UserRole::findOrFail($id);

        return response()->json([
            $userRoleData
        ], 200);
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
        $newData = $request->all();

        $dataUpdate = UserRole::findOrFail($id);

        if ($dataUpdate->update($newData)) {
            return response()->json([
                'msg' => 'User Role has been updated',
                'updatedUserRoleId' => $dataUpdate->user_role_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while updating user role'
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
        $deleteUserRole = UserRole::findOrFail($id);

        if ($deleteUserRole->delete()) {
            return response()->json([
                'msg' => 'User role has been deleted'
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while deleting user role'
        ], 500);
    }
}
