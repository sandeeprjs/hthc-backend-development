<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceablePinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('serviceable_pins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('office_type');
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('pincode_id');
            $table->string('status')->nullable();
            $table->boolean('enabled')->nullable();
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
        Schema::dropIfExists('serviceable_pins');
    }
}
