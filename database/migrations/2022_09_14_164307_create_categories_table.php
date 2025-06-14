<?php

use App\Models\Story;
use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
         // Now create the categories table as originally intended
         Schema::create('categories', static function (Blueprint $table) {
            $table->id();
            $table->string('title');

            $table->foreignIdFor(Category::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('slug');
            $table->integer('sorting')->default(0);
            $table->timestamps();
        });
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('image');
            $table->string('html');
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('story_category', static function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Story::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

           

            $table->timestamps();
        });
     

       

   
    }

    public function down(): void
    {
        Schema::dropIfExists('story_category');
        Schema::dropIfExists('categories');
    }
};
