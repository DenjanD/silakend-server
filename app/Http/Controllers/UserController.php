<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

use App\Models\User;
use App\Models\JobUnit;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::allows('is-superadmin')) {
            $userData = User::with('jobUnit')
                        ->select('user_id','nip','name','address','phone','email','unit_id')
                        ->get();
        } else if (Gate::allows('is-verifier')) {
            $userData = User::with('jobUnit')
                        ->select('user_id','nip','name','address','phone','email','unit_id')
                        ->where('unit_id',Auth::user()->jobUnit->unit_id)
                        ->get();
        } else {
            return response()->json([
                'msg' => 'Forbidden'
            ], 403);
        }

        return response()->json(
            $userData
        ,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $this->authorize('is-superadmin');
        $newData = $request->all();

         unset($newData['role_id']);

        $newData['password'] = Hash::make($newData['password']);

        $newUser = User::create($newData);

        if ($newUser->user_id != '') {
            $newUserRole['user_id'] = $newUser->user_id;
            $newUserRole['role_id'] = $request['role_id'];

            UserRole::create($newUserRole);

            return response()->json([
                'msg' => 'User has been created',
                'newUserId' => $newUser->user_id
            ], 200);
        }

        return response()->json([
            'msg' => 'Something wrong while creating new user'
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
        if (Gate::allows('is-superadmin')) {
            $userData = User::with(['jobUnit','role'])->findOrFail($id);
        } else if (Gate::allows('is-verifier')) {
            $userData = User::with(['jobUnit','role'])->where('user_id', $id)->where('unit_id', Auth::user()->jobUnit->unit_id)->first();
        } else {
            $userData = User::with(['jobUnit','role'])->where('user_id', $id)->where('user_id', Auth::user()->user_id)->first();
        }

        return response()->json([$userData], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $this->authorize('is-superadmin-or-currentuser',$id);
        // Update User's Roles Tables Only
        $deletedUserRoles = UserRole::where('user_id', $id)->delete();
        $newUserRoleIds = [];
        $i = 0;
        
        // Inserting every new assigned user roles
        if ($deletedUserRoles > 0) {
            for ($i = 0; $i < count($request->newRoles); $i++) {
                $updatedUserRoles = UserRole::create([
                        'user_id' => $id,
                        'role_id' => $request->newRoles[$i]['role_id']
                ]);
                $newUserRoleIds[$i] = $updatedUserRoles->user_role_id;
            }

            if (count($newUserRoleIds) == count($request->newRoles)) {
                return $this->userMasterUpdate($request, $id);
            } else {
                return response()->json([
                    'msg' => 'Something wrong while inserting the new roles',
                    'isUserRoleSaved' => false
                ], 500);
            }
        } else {
            return response()->json([
                'msg' => 'Something wrong while deleting the old roles',
                'isUserRoleSaved' => false
            ], 500);
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
        $this->authorize('is-superadmin');
        $userRoles = UserRole::where('user_id', $id);
        if ($userRoles->delete()) {
            $deleteUser = User::findOrFail($id);
            if ($deleteUser->delete()) {
                return response()->json([
                    'msg' => 'User has been deleted'
                ], 200);
            } else {
                return response()->json([
                    'msg' => 'There is a problem while deleting the user'
                ], 200);
            }
        } else {
            return response()->json([
                'msg' => 'There is something wrong while deleting the user\'s roles'
            ], 200);
        }
    }

    public function preStoreData() {
        $jobUnits = JobUnit::select('unit_id','name')->get();
        $roles = Role::select('role_id','name')->get();
        return response()->json([
            'jobUnits' => $jobUnits,
            'roles' => $roles
        ], 200);
    }

    public function preUpdateData($id) {
        $dataEdit = User::with(['jobUnit','role'])->where('user_id',$id)->first();
        $jobUnits = JobUnit::select('unit_id','name')->get();
        $roles = Role::select('role_id','name')->get();

        return response()->json([
            'dataEdit' => $dataEdit,
            'jobUnits' => $jobUnits,
            'roles' => $roles
        ]);
    }

    public function userMasterUpdate(Request $request, $id) {
        $newData = $request->all();
        
        $dataUpdate = User::findOrFail($id);

        // Unset role_id request before updating User's data
        unset($newData['role_id']);

        // Hash password request if not null before update
        if ($newData['password'] != '') {
            $newData['password'] = Hash::make($newData['password']);
        } else {
            $newData['password'] = $dataUpdate->password;
        }

        // Update User's data
        if ($dataUpdate->update($newData)) {
            return response()->json([
                'msg' => 'User has been updated',
                'updatedUserId' => $dataUpdate->user_id
            ], 200);
        };

        return response()->json([
            'msg' => 'Something wrong while updating the user'
        ], 500);
    }
}
