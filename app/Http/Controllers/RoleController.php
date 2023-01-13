<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;

use App\Models\Role;
use App\Events\RoleUpdate;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roleData = Role::select('role_id','name','level')
                        ->get();
        
        return response()->json($roleData, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $newData = $request->all();

        $newRole = Role::create($newData);

        if ($newRole->role_id != '') {
            //Broadcast to Front End Listener
            broadcast(new RoleUpdate("Role ".$newRole['name']." has been created"));

            return response()->json([
                'msg' => 'Role has been created',
                'newRoleId' => $newRole->role_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while creating new role'
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
        $roleData = Role::findOrFail($id);

        return response()->json([$roleData], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $newData = $request->all();
        
        $dataUpdate = Role::findOrFail($id);

        if ($dataUpdate->update($newData)) {
            //Broadcast to Front End Listener
            broadcast(new RoleUpdate("Role ".$newRole['name']." has been updated"));

            return response()->json([
                'msg' => 'Role has been updated',
                'updatedRoleId' => $dataUpdate->role_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while updating the role'
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
        $deleteRole = Role::findOrFail($id);

        if ($deleteRole->delete()) {
            //Broadcast to Front End Listener
            broadcast(new RoleUpdate("Role ".$newRole['name']." has been deleted"));

            return response()->json([
                'msg' => 'Role has been deleted',
            ], 200);
        }

        return response()->json([
            'msg' => 'There is something wrong while deleting the role'
        ], 500);
    }
}
