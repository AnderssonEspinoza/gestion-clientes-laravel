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
        Schema::table('asignacions', function (Blueprint $table) {
           //$table->string('estado')->default('asignado'); // Estado por defecto
            $table->enum('estado', ['asignado', 'finalizado'])->default('asignado');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignacions', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
