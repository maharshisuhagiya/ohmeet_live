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
        Schema::create('payment_information', function (Blueprint $table) {
            $table->id();
            $table->string('apiStatus')->nullable();
            $table->string('msg')->nullable();
            $table->string('txnStatus')->nullable();
            $table->string('txnDetails_amount')->nullable();
            $table->string('txnDetails_bankReferenceId')->nullable();
            $table->string('txnDetails_merchantTxnId')->nullable();
            $table->string('txnDetails_txnMessage')->nullable();
            $table->string('txnDetails_utrNo')->nullable();
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
        Schema::dropIfExists('payment_information');
    }
};
