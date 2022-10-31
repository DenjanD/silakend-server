<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->string('nip', 20);
            $table->string('password');
            $table->string('name', 60);
            $table->text('address');
            $table->string('phone', 14);
            $table->string('email', 60)->unique();

            $table->foreignUuid('unit_id');

            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
            
            $table->foreign('unit_id')->references('unit_id')->on('job_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
