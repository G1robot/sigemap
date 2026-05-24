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
        Schema::create('zonas', function (Blueprint $table) {
            $table->id('id_zona');
            $table->string('nombre_zona', 100);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('rutas', function (Blueprint $table) {
            $table->id('id_ruta');
            $table->unsignedBigInteger('id_zona')->nullable();
            $table->string('nombre_ruta', 100);
            $table->string('horario_permitido', 50)->nullable();
            $table->geometry('geom', subtype: 'linestring', srid: 4326)->nullable();
            $table->timestamps();

            $table->foreign('id_zona')->references('id_zona')->on('zonas')->onDelete('set null');
        });

        Schema::create('camiones', function (Blueprint $table) {
            $table->id('id_camion');
            $table->string('placa', 15)->unique();
            $table->string('modelo', 50)->nullable();
            $table->decimal('capacidad_ton', 5, 2);
            $table->string('dimension_tipo', 30);
            $table->string('estado_operativo', 30)->default('Operativo');
            $table->timestamps();
        });

        Schema::create('historial_mantenimiento', function (Blueprint $table) {
            $table->id('id_mantenimiento');
            $table->unsignedBigInteger('id_camion')->nullable();
            $table->date('fecha_ingreso');
            $table->text('descripcion')->nullable();
            $table->timestamps();

            $table->foreign('id_camion')->references('id_camion')->on('camiones')->onDelete('set null');
        });

        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombre_completo', 150);
            $table->string('ci', 20)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('usuario', 50)->unique()->nullable();
            $table->string('password', 255)->nullable();
            $table->string('cargo_base', 50);
            $table->string('rol', 30)->nullable();
            $table->string('estado', 30)->default('Activo');
            $table->timestamps();
        });

        Schema::create('asignaciones_ruta', function (Blueprint $table) {
            $table->id('id_asignacion');
            $table->unsignedBigInteger('id_camion')->nullable();
            $table->unsignedBigInteger('id_ruta')->nullable();
            $table->date('fecha');
            $table->string('turno', 30);
            $table->string('estado_operacion', 30)->default('Programada');
            $table->timestamps();

            $table->foreign('id_camion')->references('id_camion')->on('camiones')->onDelete('set null');
            $table->foreign('id_ruta')->references('id_ruta')->on('rutas')->onDelete('set null');
        });

        Schema::create('detalle_cuadrilla', function (Blueprint $table) {
            $table->unsignedBigInteger('id_asignacion');
            $table->unsignedBigInteger('id_usuario');
            $table->string('rol_en_viaje', 50);

            $table->primary(['id_asignacion', 'id_usuario']);

            $table->foreign('id_asignacion')->references('id_asignacion')->on('asignaciones_ruta')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });

        Schema::create('registro_botadero', function (Blueprint $table) {
            $table->id('id_botadero');
            $table->unsignedBigInteger('id_asignacion')->nullable();
            $table->timestamp('hora_descarga');
            $table->decimal('peso_descargado_ton', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('id_asignacion')->references('id_asignacion')->on('asignaciones_ruta')->onDelete('set null');
        });

        Schema::create('pagos_personal', function (Blueprint $table) {
            $table->id('id_pago');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->unsignedBigInteger('id_asignacion')->nullable();
            $table->decimal('monto_pago', 10, 2);
            $table->date('fecha');
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('set null');
            $table->foreign('id_asignacion')->references('id_asignacion')->on('asignaciones_ruta')->onDelete('set null');
        });

        Schema::create('contingencias_paros', function (Blueprint $table) {
            $table->id('id_contingencia');
            $table->date('fecha')->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('bloqueo_total')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contingencias_paros');
        Schema::dropIfExists('pagos_personal');
        Schema::dropIfExists('registro_botadero');
        Schema::dropIfExists('detalle_cuadrilla');
        Schema::dropIfExists('asignaciones_ruta');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('historial_mantenimiento');
        Schema::dropIfExists('camiones');
        Schema::dropIfExists('rutas');
        Schema::dropIfExists('zonas');
    }
};
