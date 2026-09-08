<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->string('color', 20)->default('#fff59d');
            $table->float('pos_x')->default(100);
            $table->float('pos_y')->default(100);
            $table->float('rotation')->default(0);
            $table->date('board_date');
            $table->timestamps();

            $table->index('board_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
