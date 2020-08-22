<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string("gamecode")->unique();
          $table->string("fullname");
          $table->string("address");
          $table->string("email")->unique();
          $table->string("phone")->unique();
          $table->string("token")->nullable();
          $table->enum('status_active', ['active', 'inactive']);
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
        Schema::dropIfExists('members');
    }
}
