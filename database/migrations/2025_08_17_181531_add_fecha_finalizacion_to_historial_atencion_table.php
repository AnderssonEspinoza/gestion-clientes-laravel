<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('historial_atencion', function (Blueprint $table) {
            $table->dateTime('fecha_finalizacion')->nullable()->after('fecha_atencion');
        });
    }

    public function down()
    {
        Schema::table('historial_atencion', function (Blueprint $table) {
            $table->dropColumn('fecha_finalizacion');
        });
    }

};
