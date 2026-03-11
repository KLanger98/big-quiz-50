<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('week_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->unique(['competition_id', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weeks');
    }
};
