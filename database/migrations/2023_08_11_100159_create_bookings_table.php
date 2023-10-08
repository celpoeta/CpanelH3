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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('business_email');
            $table->string('business_website');
            $table->string('business_address');
            $table->string('business_number');
            $table->string('business_phone');
            $table->string('business_logo');
            $table->string('booking_slots');
            $table->longText('json')->nullable();
            $table->boolean('payment_status')->default(0);
            $table->decimal('amount',10,2)->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('currency_name')->nullable();
            $table->string('payment_type')->nullable();
            $table->bigInteger('created_by')->nullable();
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
        Schema::dropIfExists('bookings');
    }
};
