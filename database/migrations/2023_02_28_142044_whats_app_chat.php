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
        Schema::create('whatsapp', function (Blueprint $table) {
            $table->id()->index();
            $table->string('wam_id')->unique();
            $table->dateTimeTz("timestamp");
            $table->string('body')->unique();
            $table->string('message_type')->unique();
            $table->string('media_url')->nullable();
            $table->string('status');
            $table->string('messaging_product');
            //$table->foreignId("user_id")->references('id')->on("contacts");
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
        Schema::dropIfExists('whatsapp');
    }
};
