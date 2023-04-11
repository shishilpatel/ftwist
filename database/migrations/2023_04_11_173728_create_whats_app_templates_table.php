<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whats_app_templates', function (Blueprint $table) {
            $table->id()->index();
            $table->string("user_id");
            $table->string("message_template_id");
            $table->string("message_template_language");
            $table->string("message_template_name");
            $table->string("template_status");
            $table->string("template_language");
            $table->string("reason")->default("NONE");
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
        Schema::dropIfExists('whats_app_templates');
    }
};
