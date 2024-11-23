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
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('photo')->nullable(); // Photo column
            $table->boolean('is_public')->default(false); // Is public column
            $table->text('description');
            $table->unsignedInteger('like_count');
            $table->timestamps(); // Created_at and updated_at columns

            // Define the foreign key constraint
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });

        Schema::create('post_user', function (Blueprint $table) { // liked posts
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_user');
    }
};
