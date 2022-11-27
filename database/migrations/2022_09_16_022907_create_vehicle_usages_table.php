<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_usages', function (Blueprint $table) {
            $table->uuid('usage_id')->primary();

            $table->foreignUuid('vehicle_id');
            $table->foreignUuid('driver_id');
            $table->foreignUuid('user_id');
            $table->foreignUuid('ucategory_id');

            $table->text('usage_description');
            $table->integer('personel_count');
            $table->text('destination');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('depart_date')->nullable();
            $table->time('depart_time')->nullable();
            $table->date('arrive_date')->nullable();
            $table->time('arrive_time')->nullable();
            $table->integer('distance_count_out')->nullable();
            $table->integer('distance_count_in')->nullable();
            $table->string('status');
            $table->text('status_description')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles');
            $table->foreign('driver_id')->references('user_id')->on('users');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('ucategory_id')->references('ucategory_id')->on('usage_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_usages');
    }
}
