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
        Schema::create('agency_coin_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('agency_id')->default(0);
            $table->integer('coin')->default(0);
            $table->integer('g_coin')->default(0);
            $table->integer('status')->default(0);
            $table->string('excel_sheet')->default(0);
            $table->text('remark')->default(0);
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
        Schema::dropIfExists('agency_coin_histories');
    }
};
