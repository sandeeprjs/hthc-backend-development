<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('branch_name');
            $table->string('branch_type')->nullable();
            $table->string('incharge_name')->nullable();
            $table->string('incharge_gender')->nullable();
            $table->string('incharge_dob')->nullable();
            $table->text('add_line_1')->nullable();
            $table->text('add_line_2')->nullable();
            $table->unsignedBigInteger('pincode_id')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('fax')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('active')->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->string('closing_days')->nullable();
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
        Schema::dropIfExists('branches');
    }
}
