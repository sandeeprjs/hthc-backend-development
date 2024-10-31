<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFranchiseeDeliveryChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('franchisee_delivery_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('franchisee_id');
            $table->string('from_weight_kgs')->nullable();
            $table->string('to_weight_kgs')->nullable();
            $table->string('price')->nullable();
            $table->string('addl_weight')->nullable();
            $table->string('addl_price')->nullable();
            $table->string('consg_type')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('franchisee_delivery_charges');
    }
}