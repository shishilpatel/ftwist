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
            $table->id();
            $table->string('msg_id')->unique();//
            $table->string('business_phone_id');//
            $table->string('business_phone_number');//
            $table->string('customer_name')->unique();
            $table->date('received_at');//
            $table->string('message')->unique();
            $table->string('conversation_id')->unique();
            $table->string('conversation_type')->nullable();
            $table->string('conversation_type_value')->nullable();

            $table->dateTimeTz('conversation_expires_at')->nullable();
            $table->string('customer_id');//
            $table->string('status_value')->nullable();
            $table->string('error')->nullable();

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
