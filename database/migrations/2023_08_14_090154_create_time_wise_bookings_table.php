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
        Schema::create('time_wise_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('slot_duration');
            $table->bigInteger('slot_duration_minutes');
            $table->boolean('payment_status')->default(0);
            $table->string('payment_type')->nullable();
            $table->text('services');
            $table->string('week_time')->nullable();
            $table->boolean('intervals_time_status')->default(0);
            $table->longText('intervals_time_json')->nullable();
            $table->boolean('limit_time_status')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('rolling_days_status')->default(0);
            $table->bigInteger('rolling_days')->nullable();
            $table->boolean('maximum_limit_status')->default(0);
            $table->bigInteger('maximum_limit')->nullable();
            $table->boolean('multiple_booking')->default(0);
            $table->boolean('email_notification')->default(0);
            $table->string('time_zone');
            $table->string('date_format');
            $table->string('time_format');
            $table->boolean('enable_note')->default(0);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('time_wise_settings');
    }
};
