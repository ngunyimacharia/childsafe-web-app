<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChildLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('child_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('url');
            $table->text('initiator');
            $table->string('timestamp');
            $table->string('cloudinary_public_id')->nullable();
            $table->string('cloudinary_version')->nullable();
            $table->string('cloudinary_url')->nullable();
            $table->string('cloudinary_secure_url')->nullable();
            $table->string('moderation_status')->nullable();
            $table->string('moderation_reasons')->nullable();
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
        Schema::dropIfExists('child_logs');
    }
}
