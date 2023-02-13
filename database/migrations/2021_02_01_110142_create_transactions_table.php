<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
                $table->Integer('customerId')->nullable(); // Customer Id
                $table->Integer('riderId')->nullable(); // Rider Id
                $table->Integer('merchantId')->nullable(); // Merchant Id
                $table->text('pickUpDestination')->nullable();
                $table->text('dropOffDestination')->nullable();
                $table->text('notes')->nullable();
                $table->text('serviceFee')->nullable();
                $table->Integer('status')->default(0);
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
        Schema::dropIfExists('transactions');
    }
}
