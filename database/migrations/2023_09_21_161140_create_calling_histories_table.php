<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calling_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->integer('opponent_user_id')->default(0);
            $table->string('call_duration')->nullable();
            $table->integer('status')->comment('1: Plus, 2: Light, 3: Lost')->default(0);
            $table->integer('total_coin')->default(0);
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
        Schema::dropIfExists('calling_histories');
    }
};
