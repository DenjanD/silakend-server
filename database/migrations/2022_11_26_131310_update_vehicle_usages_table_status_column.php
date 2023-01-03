<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\{StringType, Type};
use Illuminate\Support\Facades\{DB, Log};

class UpdateVehicleUsagesTableStatusColumn extends Migration
{
    public function __construct()
    {
        try {
            Type::hasType('enum') ?: Type::addType('enum', StringType::class);
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_usages', function (Blueprint $table) {
            $table->enum('status', ['WAITING','APPROVED','READY','PROGRESS','DONE','CANCELED','REJECTED'])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
