<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('vehicle_id')->primary();
            $table->string('name', 40);
            $table->string('year', 4);
            $table->date('tax_date');
            $table->date('valid_date');
            $table->string('license_number', 10);
            $table->integer('distance_count');

            $table->foreignUuid('vcategory_id');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('vcategory_id')->references('vcategory_id')->on('vehicle_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
