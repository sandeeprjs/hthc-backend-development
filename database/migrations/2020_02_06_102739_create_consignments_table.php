<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('consg_number')->unique();
            $table->string('office_type');
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->string('sheet_id')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->boolean('used')->nullable();
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
        Schema::dropIfExists('consignments');
    }
}
