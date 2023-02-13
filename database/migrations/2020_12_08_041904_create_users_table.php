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
            $table->bigIncrements('id');
            $table->bigInteger('facebookId')->nullable();
            $table->text('name')->nullable();
            $table->text('lastName')->nullable();
            $table->text('firstName')->nullable();
            $table->text('middleName')->nullable();
            $table->text('birthdate')->nullable();
            $table->text('phoneNumber')->nullable();
            $table->text('address')->nullable();
            $table->text('email')->nullable();
            $table->text('password')->nullable();
            $table->text('image')->nullable();
            $table->text('businessHours')->nullable();
            $table->Integer('subscriptionPlan')->default(0);
            $table->double('latitude')->default(0);
            $table->double('longitude')->default(0);
            $table->Integer('isBusy')->default(0);
            $table->Integer('type')->default(0);
            $table->Integer('status')->default(1);
            $table->Integer('isViewAds')->default(0);
            $table->Integer('businessType')->default(0);
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
        Schema::dropIfExists('users');
    }
}
