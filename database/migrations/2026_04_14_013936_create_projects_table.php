<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('summary');
            $table->string('role')->nullable();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->json('tech_stack')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('architecture_image_path')->nullable();
            $table->longText('problem')->nullable();
            $table->longText('approach')->nullable();
            $table->longText('challenges')->nullable();
            $table->longText('outcome')->nullable();
            $table->string('repo_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['is_published', 'is_featured', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
