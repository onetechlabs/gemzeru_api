<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TablePaymentInstruments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_instruments', function (Blueprint $table) {
            $table->bigIncrements('id');
            //gold, heart, coin
            $table->string("payment_instrument");
            $table->string("gamecode");
            $table->string("description")->nullable();
            $table->enum('is_used', ['yes', 'no']);
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
        Schema::dropIfExists('payment_instruments');
    }
}
