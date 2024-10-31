<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispatches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('dispatch_code')->nullable();
            $table->string('org_office_type')->nullable();
            $table->string('consg_number')->nullable();
            $table->unsignedBigInteger('org_office_id')->nullable();
            $table->string('dest_office_type')->nullable();
            $table->string('dest_office_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('mode_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->unsignedBigInteger('load_sender_user_id')->nullable();
            $table->dateTime('departure_datetime')->nullable();
            $table->dateTime('arrival_datetime')->nullable();
            $table->string('baggage_cost')->nullable();
            $table->string('baggage_weight')->nullable();
            $table->string('trip_sheet_number')->nullable();
            $table->string('bag_manifest_number')->nullable();
            $table->string('length')->nullable();
            $table->string('breadth')->nullable();
            $table->string('height')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('dispatches');
    }
}
