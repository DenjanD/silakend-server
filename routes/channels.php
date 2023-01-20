<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user', function ($user) {
    return $user;
});

Broadcast::channel('role', function ($role) {
    return $role;
});

Broadcast::channel('vehicleusage', function ($vehicleusage) {
    return $vehicleusage;
});

Broadcast::channel('vehiclemaintenance', function ($vehiclemaintenance) {
    return $vehiclemaintenance;
});

Broadcast::channel('vehiclemaintenancedetail', function ($vehiclemaintenancedetail) {
    return $vehiclemaintenancedetail;
});

Broadcast::channel('jobunit', function ($jobunit) {
    return $jobunit;
});

Broadcast::channel('usagecategory', function ($usagecategory) {
    return $usagecategory;
});

Broadcast::channel('userrole', function ($userrole) {
    return $userrole;
});

Broadcast::channel('vehiclecategory', function ($vehiclecategory) {
    return $vehiclecategory;
});

Broadcast::channel('vehicle', function ($vehicle) {
    return $vehicle;
});