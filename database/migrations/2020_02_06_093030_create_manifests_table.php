<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManifestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('manifest_datetime')->nullable();
            $table->string('manifest_number')->nullable();
            $table->string('manifest_type')->nullable();
            $table->unsignedBigInteger('consg_number_id');
            $table->double('total_weight_kg')->nullable();
            $table->unsignedBigInteger('origin_branch_id')->nullable();
            $table->unsignedBigInteger('dest_branch_id')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->string('sender_type')->nullable();
            $table->string('receiver_type')->nullable();
            $table->unsignedBigInteger('dest_pincode_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->string('office_type')->nullable();
            $table->boolean('customer_view')->default(true);
            $table->boolean('last_mile_delivery')->default(false);
            $table->unsignedBigInteger('delivery_user_id')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('manifests');
    }
}
