<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_it_staff')->default(false);
        });

        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('email')->nullable()->unique();
            $table->string('department')->nullable();
            $table->timestamps();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('type', 32)->index();
            $table->string('equipment_kind')->nullable();
            $table->string('asset_tag')->nullable()->unique();
            $table->string('serial_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('location')->nullable();
            $table->string('hostname')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->foreignId('person_id')->nullable()->index()->constrained('people')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('asset_knowledge_article', function (Blueprint $table) {
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('knowledge_article_id')->index()->constrained()->cascadeOnDelete();
            $table->primary(['asset_id', 'knowledge_article_id']);
        });

        Schema::create('knowledge_article_person', function (Blueprint $table) {
            $table->foreignId('knowledge_article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->index()->constrained('people')->cascadeOnDelete();
            $table->primary(['knowledge_article_id', 'person_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_article_person');
        Schema::dropIfExists('asset_knowledge_article');
        Schema::dropIfExists('knowledge_articles');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('people');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_it_staff');
        });
    }
};
