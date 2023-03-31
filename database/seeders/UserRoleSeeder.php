<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        // get new user id after seeding user
        $newUserId = DB::table('users')->select('user_id')->first();

        // get new role id after seeding role
        $newRoleId = DB::table('roles')->select('role_id')->first();

        DB::table('user_roles')->insert([
            'user_role_id' => $faker->uuid,
            'user_id' => $newUserId->user_id,
            'role_id' => $newRoleId->role_id,
            'deleted_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
