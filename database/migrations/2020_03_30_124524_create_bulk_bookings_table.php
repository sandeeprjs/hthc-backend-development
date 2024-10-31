<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBulkBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('consg_number')->nullable();
            $table->string('consg_type')->nullable();
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
            $table->string('booked_amount')->nullable();
            $table->string('amount_due')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('payment_id')->nullable();
            $table->boolean('insured')->nullable();
            $table->string('insured_by')->nullable();
            $table->string('declared_consg_value')->nullable();
            $table->boolean('sms_to_sender')->nullable();
            $table->boolean('sms_to_receiver')->nullable();
            $table->string('origin_office_type')->nullable();
            $table->unsignedBigInteger('origin_office_id')->nullable();

            $table->string('receiver_name')->nullable();
            $table->text('receiver_add_line_1')->nullable();
            $table->text('receiver_add_line_2')->nullable();
            $table->string('receiver_district')->nullable();
            $table->string('receiver_city')->nullable();
            $table->string('receiver_state')->nullable();
            $table->unsignedBigInteger('receiver_pincode_id')->nullable();
            $table->string('wrong_pincode')->nullable();
            $table->unsignedBigInteger('receiver_country_id')->nullable();
            $table->string('receiver_mobile_number')->nullable();
            $table->string('receiver_phone_number')->nullable();
            $table->string('receiver_email')->nullable();
            $table->boolean('has_error')->nullable();
            $table->text('reamrks')->nullable();
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
        Schema::dropIfExists('bulk_bookings');
    }
}