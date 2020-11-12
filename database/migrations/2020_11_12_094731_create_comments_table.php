<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('web_link');
            $table->foreignId('remote_user_id')->constrained()->cascadeOnDelete();
            $table->boolean('isDeleted');
            $table->foreignId('pull_request_id')->constrained()->cascadeOnDelete();
            $table->dateTime('repository_created_at');
            $table->dateTime('repository_updated_at');
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
        Schema::dropIfExists('comments');
    }
}
