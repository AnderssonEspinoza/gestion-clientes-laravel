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
        Schema::create('historial_atencion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asignacion_id'); // vínculo directo al cliente+asesor
            $table->enum('resultado', [
                'venta_concretada',
                'no_califica',
                'inactivo',
                'seguimiento',
            ]); // cómo terminó la atención
            $table->text('observaciones')->nullable(); // notas adicionales
            $table->timestamp('fecha_atencion')->useCurrent();

            $table->timestamps();

            $table->foreign('asignacion_id')
                ->references('id')
                ->on('asignacions')
                ->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_atencion');
    }
};
