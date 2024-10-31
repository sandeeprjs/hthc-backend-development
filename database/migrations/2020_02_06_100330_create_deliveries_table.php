<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id');
            $table->string('receiver_name')->nullable();
            $table->text('add_line_1')->nullable();
            $table->text('add_line_2')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->unsignedBigInteger('pincode_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('office_type')->nullable();
            $table->string('office_id')->nullable();
            $table->text('remarks')->nullable();
            $table->string('delivery_status')->nullable();
            $table->timestamp('delivery_datetime')->nullable();
            $table->unsignedBigInteger('delivery_user_id')->nullable();
            $table->tinyInteger('no_of_attempts')->nullable();
            $table->tinyInteger('no_of_pieces')->nullable();
            $table->string('penalty')->nullable();
            $table->string('tookstatus')->nullable();
            $table->string('rec_name')->nullable();
            $table->string('actual_delivery_charge')->nullable();
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
        Schema::dropIfExists('deliveries');
    }
}
