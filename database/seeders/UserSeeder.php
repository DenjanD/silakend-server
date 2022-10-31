<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
 
    	for($i = 1; $i <= 50; $i++){
 
    	      // insert data ke table pegawai menggunakan Faker
    		DB::table('users')->insert([
                'user_id' => $faker->uuid,
    			'nip' => $faker->randomNumber,
    			'password' => '$2y$10$b1kVsxMWfjWnXTlLgk.kCOb4Z88b1Jut8PnFKEhCQkBQQaZYXnN0S',
    			'name' => $faker->name,
    			'address' => $faker->address,
                'phone' => '6284728392',
                'email' => $faker->email,
                'unit_id' => 'e8eece22-5746-11ed-a019-d8c49710dd2f',
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
    		]);
 
    	}
    }
}
