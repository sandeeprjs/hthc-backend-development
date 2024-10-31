<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFranchiseesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('franchisees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branch_id');
            $table->string('code')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_code')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('mobile_verification_code')->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('enterprise_name')->nullable();
            $table->text('add_line_1')->nullable();
            $table->text('add_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('pincode_id')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_gender')->nullable();
            $table->dateTime('contact_person_dob')->nullable();
            $table->string('contact_person_mobile')->nullable();
            $table->string('contact_person_email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('fax')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('branch_type')->nullable();
            $table->string('active')->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->string('closing_days')->nullable();
            $table->string('franchisee_type')->nullable();
            $table->string('current_bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('avatar')->nullable();
            $table->string('doc_proof')->nullable();
            $table->string('remark')->nullable();
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
        Schema::dropIfExists('franchisees');
    }
}
