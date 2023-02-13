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
            $table->bigIncrements('id');
            $table->text('brand')->nullable();
            $table->text('model')->nullable();
            $table->text('manufacturer')->nullable();
            $table->text('plateNumber')->nullable();
            $table->text('color')->nullable();
            $table->text('description')->nullable();
            $table->Integer('type')->nullable();
            $table->Integer('status')->defeault(1);
            $table->timestamps();
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
