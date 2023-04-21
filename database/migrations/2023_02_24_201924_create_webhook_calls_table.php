<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('webhook_calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('payload')->nullable();

            $table->timestamps();
        });
    }
};
