<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_maintenances', function (Blueprint $table) {
            $table->uuid('maintenance_id')->primary();
            $table->foreignUuid('vehicle_id');
            $table->date('date');
            $table->string('category', 40);
            $table->text('description');
            $table->integer('total_cost');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_maintenances');
    }
}
