<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_managers', function (Blueprint $table) {
            $table->id()->index();
            $table->string('business_phone_id');
            $table->string('business_phone_number');
            $table->text('access_token');
            $table->string('business_manager_id');
            $table->string('webhook_token');
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
        Schema::dropIfExists('business_managers');
    }
};
