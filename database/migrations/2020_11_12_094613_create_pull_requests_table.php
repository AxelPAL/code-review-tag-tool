<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePullRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pull_requests', function (Blueprint $table) {
            $table->id();
            $table->string('web_link');
            $table->string('title');
            $table->longText('description');
            $table->integer('remote_id');
            $table->string('destination_branch');
            $table->string('destination_commit');
            $table->dateTime('repository_created_at');
            $table->dateTime('repository_updated_at');
            $table->integer('comment_count');
            $table->string('state');
            $table->foreignId('remote_author_id')->constrained('remote_users');
            $table->foreignId('closed_by_remote_user_id')->nullable()->constrained('remote_users');
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
        Schema::dropIfExists('pull_requests');
    }
}
