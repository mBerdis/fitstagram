<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->string('name');

            $table->timestamps(); // Created_at and updated_at columns

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('group_join_requests', function (Blueprint $table)
        {
            $table->id(); // Primary key

            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });


        Schema::create('group_user', function (Blueprint $table) {
            $table->id(); // Primary key

            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });

        Schema::create('group_post', function (Blueprint $table) {
            $table->id(); // Primary key

            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
        Schema::dropIfExists('group_join_requests');
        Schema::dropIfExists('group_user');
        Schema::dropIfExists('group_post');
    }
};
