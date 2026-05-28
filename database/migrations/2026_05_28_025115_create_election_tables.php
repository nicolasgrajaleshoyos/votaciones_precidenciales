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
        // 1. candidatos
        Schema::create('candidatos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('formula_vice', 150)->nullable();
            $table->string('partido', 150)->nullable();
            $table->string('tendencia'); // izquierda, centro-izquierda, centro, centro-derecha, derecha
            $table->string('foto_url', 500)->nullable();
            $table->text('biografia')->nullable();
            $table->text('propuestas')->nullable();
            $table->decimal('favorabilidad', 5, 2)->default(0.00);
            $table->decimal('tendencia_redes', 5, 2)->default(0.00);
            $table->decimal('crecimiento_semanal', 5, 2)->default(0.00);
            $table->string('color_partido', 7)->default('#666666');
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamps();
        });

        // 2. votos
        Schema::create('votos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->unique()->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('candidato_id')->constrained('candidatos')->cascadeOnDelete();
            $table->timestamp('fecha_voto')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        // 3. encuestas
        Schema::create('encuestas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('tipo'); // primera_vuelta, segunda_vuelta
            $table->string('fuente', 200)->nullable();
            $table->date('fecha_realizacion')->nullable();
            $table->decimal('margen_error', 4, 2)->nullable();
            $table->integer('muestra')->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamps();
        });

        // 4. encuesta_resultados
        Schema::create('encuesta_resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id')->constrained('encuestas')->cascadeOnDelete();
            $table->foreignId('candidato_id')->constrained('candidatos')->cascadeOnDelete();
            $table->decimal('porcentaje', 5, 2);
            $table->unique(['encuesta_id', 'candidato_id']);
            $table->timestamps();
        });

        // 5. noticias
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 300);
            $table->text('contenido');
            $table->string('resumen', 500)->nullable();
            $table->string('imagen_url', 500)->nullable();
            $table->string('fuente', 200)->nullable();
            $table->string('autor', 150)->nullable();
            $table->string('categoria')->default('politica'); // politica, encuestas, debate, economia, social, internacional
            $table->boolean('destacada')->default(false);
            $table->timestamp('fecha_publicacion')->useCurrent();
            $table->timestamps();
        });

        // 6. tendencias_redes
        Schema::create('tendencias_redes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidato_id')->constrained('candidatos')->cascadeOnDelete();
            $table->string('plataforma'); // twitter, facebook, tiktok, instagram, youtube
            $table->integer('menciones')->default(0);
            $table->decimal('sentimiento', 5, 2)->default(0.00);
            $table->integer('seguidores')->default(0);
            $table->integer('interacciones')->default(0);
            $table->string('hashtag_principal', 100)->nullable();
            $table->date('fecha_registro');
            $table->timestamps();
        });

        // 7. comentarios
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->text('contenido');
            $table->boolean('aprobado')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamps();
        });

        // 8. predicciones_historico
        Schema::create('predicciones_historico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidato_id')->constrained('candidatos')->cascadeOnDelete();
            $table->decimal('probabilidad', 5, 2);
            $table->decimal('intencion_voto', 5, 2)->nullable();
            $table->decimal('favorabilidad', 5, 2)->nullable();
            $table->decimal('redes_score', 5, 2)->nullable();
            $table->decimal('crecimiento', 5, 2)->nullable();
            $table->timestamp('fecha_calculo')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predicciones_historico');
        Schema::dropIfExists('comentarios');
        Schema::dropIfExists('tendencias_redes');
        Schema::dropIfExists('noticias');
        Schema::dropIfExists('encuesta_resultados');
        Schema::dropIfExists('encuestas');
        Schema::dropIfExists('votos');
        Schema::dropIfExists('candidatos');
    }
};
