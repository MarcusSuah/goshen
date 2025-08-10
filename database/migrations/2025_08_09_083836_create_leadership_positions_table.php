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
        Schema::create('leadership_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Community Leader, Block Leader, Youth Leader, Elder, etc.
            $table->text('description')->nullable();
            $table->integer('hierarchy_level'); // 1=Community, 2=Block, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leadership_positions');
    }
};
