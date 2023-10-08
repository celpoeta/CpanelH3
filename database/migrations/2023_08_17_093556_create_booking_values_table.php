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
        Schema::create('booking_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->longText('json');
            $table->float('amount')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('currency_name')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->date('booking_slots_date')->nullable();
            $table->text('booking_slots')->nullable();
            $table->date('booking_seats_date')->nullable();
            $table->string('booking_seats_session')->nullable();
            $table->text('booking_seats')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('booking_status')->default(1);
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
        Schema::dropIfExists('booking_values');
    }
};
