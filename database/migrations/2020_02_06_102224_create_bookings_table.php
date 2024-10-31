<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('consg_number');
            $table->string('consg_type');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('add_line_1')->nullable();
            $table->text('add_line_2')->nullable();
            $table->string('landmark')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->unsignedBigInteger('pincode_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('weight')->nullable();
            $table->string('final_weight')->nullable();
            $table->string('vol_weight')->nullable();
            $table->string('booking_status')->nullable();
            $table->string('height')->nullable();
            $table->string('breadth')->nullable();
            $table->string('length')->nullable();
            $table->string('final_height')->nullable();
            $table->string('final_breadth')->nullable();
            $table->string('final_length')->nullable();
            $table->timestamp('booking_date')->nullable();
            $table->string('booked_amount')->nullable();
            $table->string('final_amount')->nullable();
            $table->string('amount_due')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('payment_id')->nullable();
            $table->boolean('insured')->nullable();
            $table->string('insured_by')->nullable();
            $table->string('declared_consg_value')->nullable();
            $table->string('insurance_amt')->nullable();
            $table->string('origin_office_type')->nullable();
            $table->unsignedBigInteger('origin_office_id')->nullable();
            $table->unsignedBigInteger('dest_branch_id')->nullable();
            $table->unsignedBigInteger('booking_user_id')->nullable();
            $table->boolean('booking_modified')->nullable();
            $table->string('status')->nullable();
            $table->boolean('sms_to_sender')->nullable();
            $table->boolean('sms_to_receiver')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('mode_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

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
}
