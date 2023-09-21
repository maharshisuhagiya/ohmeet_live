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
        Schema::create('gu_payment_information', function (Blueprint $table) {
            $table->id();
            $table->string('amount')->nullable();
            $table->string('status')->nullable();
            $table->string('txnid')->nullable();
            $table->string('utr')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('payid')->nullable();
            $table->string('api_calling_status')->nullable();
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
        Schema::dropIfExists('gu_payment_information');
    }
};
