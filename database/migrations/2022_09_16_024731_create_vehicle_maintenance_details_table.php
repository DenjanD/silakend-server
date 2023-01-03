<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleMaintenanceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_maintenance_details', function (Blueprint $table) {
            $table->uuid('detail_id')->primary();
            $table->foreignUuid('maintenance_id');
            $table->string('item_name', 50);
            $table->integer('item_qty');
            $table->string('item_unit', 10);
            $table->integer('item_price');
            $table->integer('price_total');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('maintenance_id')->references('maintenance_id')->on('vehicle_maintenances');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_maintenance_details');
    }
}
