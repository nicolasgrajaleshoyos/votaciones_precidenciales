<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Candidato;
use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Models\Noticia;
use App\Models\TendenciaRedes;
use App\Models\Comentario;
use App\Models\Voto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. CANDIDATOS
        $candidatos = [
            [
                'id' => 1,
                'nombre' => 'Iván Cepeda',
                'formula_vice' => 'Aida Quilcué',
                'partido' => 'Pacto Histórico',
                'tendencia' => 'izquierda',
                'biografia' => 'Senador de la República, defensor de derechos humanos, exrepresentante a la Cámara. Reconocido por su lucha contra el paramilitarismo y la defensa de las víctimas del conflicto armado.',
                'propuestas' => 'Reforma agraria integral, justicia social, paz total, transición energética, educación pública gratuita.',
                'favorabilidad' => 62.50,
                'tendencia_redes' => 78.30,
                'crecimiento_semanal' => 3.20,
                'color_partido' => '#7B1FA2',
                'foto_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 2,
                'nombre' => 'Paloma Valencia',
                'formula_vice' => 'Juan Daniel Oviedo',
                'partido' => 'Centro Democrático',
                'tendencia' => 'derecha',
                'biografia' => 'Senadora del Centro Democrático, abogada y politóloga. Defensora de la seguridad, la inversión privada y las políticas de derecha moderna.',
                'propuestas' => 'Seguridad ciudadana, reducción de impuestos, apoyo a las fuerzas armadas, emprendimiento, lucha contra la corrupción.',
                'favorabilidad' => 55.80,
                'tendencia_redes' => 65.40,
                'crecimiento_semanal' => 2.80,
                'color_partido' => '#1565C0',
                'foto_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 3,
                'nombre' => 'Abelardo de la Espriella',
                'formula_vice' => 'José Manuel Restrepo',
                'partido' => 'Independiente',
                'tendencia' => 'derecha',
                'biografia' => 'Abogado penalista, empresario y figura mediática. Se presenta como outsider político con un discurso directo y polémico.',
                'propuestas' => 'Reforma judicial, mano dura contra la delincuencia, desburocratización del estado, apoyo al sector privado.',
                'favorabilidad' => 48.20,
                'tendencia_redes' => 72.10,
                'crecimiento_semanal' => 4.50,
                'color_partido' => '#E65100',
                'foto_url' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 4,
                'nombre' => 'Sergio Fajardo',
                'formula_vice' => 'Edna Bonilla',
                'partido' => 'Compromiso Ciudadano',
                'tendencia' => 'centro',
                'biografia' => 'Exgobernador de Antioquia, exalcalde de Medellín, matemático y político de centro. Conocido por su gestión en educación y cultura.',
                'propuestas' => 'Educación como prioridad, lucha contra la corrupción, desarrollo sostenible, reconciliación nacional.',
                'favorabilidad' => 51.30,
                'tendencia_redes' => 45.60,
                'crecimiento_semanal' => 1.20,
                'color_partido' => '#2E7D32',
                'foto_url' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 5,
                'nombre' => 'Claudia López',
                'formula_vice' => 'Leonardo Huerta',
                'partido' => 'Alianza Verde',
                'tendencia' => 'centro',
                'biografia' => 'Exalcaldesa de Bogotá, politóloga y periodista investigativa. Primera mujer elegida alcaldesa de Bogotá.',
                'propuestas' => 'Movilidad sostenible, equidad de género, medio ambiente, transparencia gubernamental, innovación tecnológica.',
                'favorabilidad' => 53.70,
                'tendencia_redes' => 58.90,
                'crecimiento_semanal' => 2.10,
                'color_partido' => '#00C853',
                'foto_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 6,
                'nombre' => 'Roy Barreras',
                'formula_vice' => 'Martha Lucía Zamora',
                'partido' => 'Partido de la U',
                'tendencia' => 'centro-izquierda',
                'biografia' => 'Senador, médico cirujano y embajador. Fue presidente del Congreso y negociador del proceso de paz.',
                'propuestas' => 'Continuidad del proceso de paz, reforma de salud, desarrollo rural, fortalecimiento institucional.',
                'favorabilidad' => 42.10,
                'tendencia_redes' => 38.50,
                'crecimiento_semanal' => -0.80,
                'color_partido' => '#FF8F00',
                'foto_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 7,
                'nombre' => 'Carlos Caicedo',
                'formula_vice' => 'Nelson Alarcón',
                'partido' => 'Fuerza Ciudadana',
                'tendencia' => 'izquierda',
                'biografia' => 'Exgobernador del Magdalena, líder del movimiento Fuerza Ciudadana. Reconocido por su lucha anticorrupción en la costa.',
                'propuestas' => 'Descentralización, lucha contra la corrupción, desarrollo regional, educación pública, economía popular.',
                'favorabilidad' => 38.50,
                'tendencia_redes' => 35.20,
                'crecimiento_semanal' => 0.50,
                'color_partido' => '#D32F2F',
                'foto_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 8,
                'nombre' => 'Luis Gilberto Murillo',
                'formula_vice' => 'Luz María Zapata',
                'partido' => 'Colombia Renaciente',
                'tendencia' => 'izquierda',
                'biografia' => 'Exministro de Ambiente, exgobernador del Chocó, ingeniero de minas. Líder afrocolombiano con amplia experiencia gubernamental.',
                'propuestas' => 'Justicia ambiental, inclusión étnica, desarrollo del Pacífico, transición energética, equidad social.',
                'favorabilidad' => 35.40,
                'tendencia_redes' => 32.80,
                'crecimiento_semanal' => 0.30,
                'color_partido' => '#4A148C',
                'foto_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 9,
                'nombre' => 'Mauricio Lizcano',
                'formula_vice' => 'Pedro de la Torre',
                'partido' => 'Independiente',
                'tendencia' => 'centro',
                'biografia' => 'Ministro de las TIC, exsenador por Caldas. Impulsor de la transformación digital del gobierno.',
                'propuestas' => 'Transformación digital, conectividad rural, innovación, emprendimiento tecnológico, modernización del estado.',
                'favorabilidad' => 40.20,
                'tendencia_redes' => 42.30,
                'crecimiento_semanal' => 1.50,
                'color_partido' => '#0097A7',
                'foto_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 10,
                'nombre' => 'Santiago Botero',
                'formula_vice' => 'Carlos Cuevas',
                'partido' => 'Independiente',
                'tendencia' => 'derecha',
                'biografia' => 'Empresario y figura política emergente. Se presenta como candidato del sector productivo y empresarial del país.',
                'propuestas' => 'Economía de mercado, reducción del estado, incentivos empresariales, infraestructura, comercio internacional.',
                'favorabilidad' => 28.60,
                'tendencia_redes' => 25.40,
                'crecimiento_semanal' => 0.80,
                'color_partido' => '#37474F',
                'foto_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 11,
                'nombre' => 'Miguel Uribe Londoño',
                'formula_vice' => 'Luisa Fernanda Villegas',
                'partido' => 'Centro Democrático',
                'tendencia' => 'derecha',
                'biografia' => 'Exconcejal de Bogotá, exsecretario de gobierno. Joven líder del Centro Democrático con enfoque en seguridad urbana.',
                'propuestas' => 'Seguridad urbana, justicia efectiva, empleo juvenil, desarrollo económico, lucha contra el narcotráfico.',
                'favorabilidad' => 44.30,
                'tendencia_redes' => 48.70,
                'crecimiento_semanal' => 1.80,
                'color_partido' => '#1A237E',
                'foto_url' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 12,
                'nombre' => 'Sondra Macollins',
                'formula_vice' => 'Leonardo Karam Helo',
                'partido' => 'Independiente',
                'tendencia' => 'centro-derecha',
                'biografia' => 'Empresaria, activista y candidata independiente. Propone un modelo de gobierno basado en la eficiencia del sector privado.',
                'propuestas' => 'Gobierno eficiente, emprendimiento femenino, educación bilingüe, política exterior activa, turismo.',
                'favorabilidad' => 22.40,
                'tendencia_redes' => 18.90,
                'crecimiento_semanal' => 0.20,
                'color_partido' => '#AD1457',
                'foto_url' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'id' => 13,
                'nombre' => 'Gustavo Matamoros',
                'formula_vice' => 'Mila María Paz',
                'partido' => 'Independiente',
                'tendencia' => 'centro',
                'biografia' => 'Periodista, analista político y candidato independiente. Enfoque en la transparencia mediática y gobierno abierto.',
                'propuestas' => 'Gobierno abierto, transparencia total, participación ciudadana, reforma política, medios libres.',
                'favorabilidad' => 20.10,
                'tendencia_redes' => 15.60,
                'crecimiento_semanal' => -0.30,
                'color_partido' => '#546E7A',
                'foto_url' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=400'
            ],
        ];

        foreach ($candidatos as $candidato) {
            Candidato::create($candidato);
        }

        // 2. USUARIO ADMIN Y DE PRUEBA
        // Usamos la contraseña Admin2026! correspondiente al hash bcrypt del seed sql
        $passHash = '$2a$10$8K1p/a0dL1LXMIgoEDFrwOfMQkf/1Z3Q5Y1vR5kO8sU8Y5xAq.Cmu';

        User::create([
            'id' => 1,
            'nombre' => 'Administrador',
            'email' => 'admin@elecciones2026.co',
            'password_hash' => $passHash,
            'rol' => 'admin',
            'cedula' => '1000000001',
            'departamento' => 'Cundinamarca',
            'ciudad' => 'Bogotá',
            'activo' => true
        ]);

        $usuarios = [
            ['id' => 2, 'nombre' => 'María García', 'email' => 'maria@ejemplo.co', 'password_hash' => $passHash, 'rol' => 'usuario', 'cedula' => '1000000002', 'departamento' => 'Antioquia', 'ciudad' => 'Medellín'],
            ['id' => 3, 'nombre' => 'Carlos Rodríguez', 'email' => 'carlos@ejemplo.co', 'password_hash' => $passHash, 'rol' => 'usuario', 'cedula' => '1000000003', 'departamento' => 'Valle del Cauca', 'ciudad' => 'Cali'],
            ['id' => 4, 'nombre' => 'Ana Martínez', 'email' => 'ana@ejemplo.co', 'password_hash' => $passHash, 'rol' => 'usuario', 'cedula' => '1000000004', 'departamento' => 'Atlántico', 'ciudad' => 'Barranquilla'],
            ['id' => 5, 'nombre' => 'Juan López', 'email' => 'juan@ejemplo.co', 'password_hash' => $passHash, 'rol' => 'usuario', 'cedula' => '1000000005', 'departamento' => 'Santander', 'ciudad' => 'Bucaramanga'],
            ['id' => 6, 'nombre' => 'Laura Hernández', 'email' => 'laura@ejemplo.co', 'password_hash' => $passHash, 'rol' => 'usuario', 'cedula' => '1000000006', 'departamento' => 'Bolívar', 'ciudad' => 'Cartagena'],
        ];

        foreach ($usuarios as $usuario) {
            User::create($usuario);
        }

        // 3. ENCUESTAS
        $encuestas = [
            ['id' => 1, 'titulo' => 'Gran Encuesta Nacional - Mayo 2026', 'tipo' => 'primera_vuelta', 'fuente' => 'Invamer-Gallup', 'fecha_realizacion' => '2026-05-20', 'margen_error' => 2.80, 'muestra' => 3200, 'descripcion' => 'Encuesta nacional de intención de voto para primera vuelta presidencial.'],
            ['id' => 2, 'titulo' => 'Encuesta Datexco Mayo 2026', 'tipo' => 'primera_vuelta', 'fuente' => 'Datexco', 'fecha_realizacion' => '2026-05-18', 'margen_error' => 3.10, 'muestra' => 2800, 'descripcion' => 'Medición de intención de voto en principales ciudades del país.'],
            ['id' => 3, 'titulo' => 'Encuesta CNC - Segundo escenario', 'tipo' => 'segunda_vuelta', 'fuente' => 'Centro Nacional de Consultoría', 'fecha_realizacion' => '2026-05-22', 'margen_error' => 2.50, 'muestra' => 3500, 'descripcion' => 'Escenario de segunda vuelta entre los dos candidatos con mayor intención de voto.'],
            ['id' => 4, 'titulo' => 'Encuesta Guarumo-EcoAnalítica', 'tipo' => 'primera_vuelta', 'fuente' => 'Guarumo-EcoAnalítica', 'fecha_realizacion' => '2026-05-15', 'margen_error' => 3.00, 'muestra' => 2500, 'descripcion' => 'Encuesta de opinión pública sobre preferencias electorales.'],
            ['id' => 5, 'titulo' => 'Tracking Electoral Semana', 'tipo' => 'primera_vuelta', 'fuente' => 'Revista Semana - Cifras y Conceptos', 'fecha_realizacion' => '2026-05-25', 'margen_error' => 2.90, 'muestra' => 3000, 'descripcion' => 'Seguimiento semanal de intención de voto a nivel nacional.'],
        ];

        foreach ($encuestas as $encuesta) {
            Encuesta::create($encuesta);
        }

        // 4. ENCUESTA RESULTADOS
        $resultados = [
            // Gran Encuesta Nacional (ID: 1)
            ['encuesta_id' => 1, 'candidato_id' => 1, 'porcentaje' => 28.50],
            ['encuesta_id' => 1, 'candidato_id' => 2, 'porcentaje' => 18.30],
            ['encuesta_id' => 1, 'candidato_id' => 3, 'porcentaje' => 12.40],
            ['encuesta_id' => 1, 'candidato_id' => 4, 'porcentaje' => 9.80],
            ['encuesta_id' => 1, 'candidato_id' => 5, 'porcentaje' => 10.50],
            ['encuesta_id' => 1, 'candidato_id' => 6, 'porcentaje' => 5.20],
            ['encuesta_id' => 1, 'candidato_id' => 7, 'porcentaje' => 4.10],
            ['encuesta_id' => 1, 'candidato_id' => 8, 'porcentaje' => 3.20],
            ['encuesta_id' => 1, 'candidato_id' => 9, 'porcentaje' => 3.50],
            ['encuesta_id' => 1, 'candidato_id' => 10, 'porcentaje' => 1.20],
            ['encuesta_id' => 1, 'candidato_id' => 11, 'porcentaje' => 2.80],
            ['encuesta_id' => 1, 'candidato_id' => 12, 'porcentaje' => 0.30],
            ['encuesta_id' => 1, 'candidato_id' => 13, 'porcentaje' => 0.20],

            // Datexco (ID: 2)
            ['encuesta_id' => 2, 'candidato_id' => 1, 'porcentaje' => 26.80],
            ['encuesta_id' => 2, 'candidato_id' => 2, 'porcentaje' => 19.50],
            ['encuesta_id' => 2, 'candidato_id' => 3, 'porcentaje' => 14.20],
            ['encuesta_id' => 2, 'candidato_id' => 4, 'porcentaje' => 8.90],
            ['encuesta_id' => 2, 'candidato_id' => 5, 'porcentaje' => 11.30],
            ['encuesta_id' => 2, 'candidato_id' => 6, 'porcentaje' => 4.80],
            ['encuesta_id' => 2, 'candidato_id' => 7, 'porcentaje' => 3.90],
            ['encuesta_id' => 2, 'candidato_id' => 8, 'porcentaje' => 2.80],
            ['encuesta_id' => 2, 'candidato_id' => 9, 'porcentaje' => 3.10],
            ['encuesta_id' => 2, 'candidato_id' => 10, 'porcentaje' => 1.50],
            ['encuesta_id' => 2, 'candidato_id' => 11, 'porcentaje' => 2.50],
            ['encuesta_id' => 2, 'candidato_id' => 12, 'porcentaje' => 0.40],
            ['encuesta_id' => 2, 'candidato_id' => 13, 'porcentaje' => 0.30],

            // Segunda Vuelta (ID: 3)
            ['encuesta_id' => 3, 'candidato_id' => 1, 'porcentaje' => 52.30],
            ['encuesta_id' => 3, 'candidato_id' => 2, 'porcentaje' => 47.70],

            // Guarumo (ID: 4)
            ['encuesta_id' => 4, 'candidato_id' => 1, 'porcentaje' => 27.10],
            ['encuesta_id' => 4, 'candidato_id' => 2, 'porcentaje' => 17.80],
            ['encuesta_id' => 4, 'candidato_id' => 3, 'porcentaje' => 13.50],
            ['encuesta_id' => 4, 'candidato_id' => 4, 'porcentaje' => 10.20],
            ['encuesta_id' => 4, 'candidato_id' => 5, 'porcentaje' => 9.80],
            ['encuesta_id' => 4, 'candidato_id' => 6, 'porcentaje' => 5.50],
            ['encuesta_id' => 4, 'candidato_id' => 7, 'porcentaje' => 4.30],
            ['encuesta_id' => 4, 'candidato_id' => 8, 'porcentaje' => 3.50],
            ['encuesta_id' => 4, 'candidato_id' => 9, 'porcentaje' => 3.80],
            ['encuesta_id' => 4, 'candidato_id' => 10, 'porcentaje' => 1.40],
            ['encuesta_id' => 4, 'candidato_id' => 11, 'porcentaje' => 2.60],
            ['encuesta_id' => 4, 'candidato_id' => 12, 'porcentaje' => 0.30],
            ['encuesta_id' => 4, 'candidato_id' => 13, 'porcentaje' => 0.20],

            // Tracking Semana (ID: 5)
            ['encuesta_id' => 5, 'candidato_id' => 1, 'porcentaje' => 29.20],
            ['encuesta_id' => 5, 'candidato_id' => 2, 'porcentaje' => 18.90],
            ['encuesta_id' => 5, 'candidato_id' => 3, 'porcentaje' => 11.80],
            ['encuesta_id' => 5, 'candidato_id' => 4, 'porcentaje' => 9.50],
            ['encuesta_id' => 5, 'candidato_id' => 5, 'porcentaje' => 10.80],
            ['encuesta_id' => 5, 'candidato_id' => 6, 'porcentaje' => 5.00],
            ['encuesta_id' => 5, 'candidato_id' => 7, 'porcentaje' => 3.80],
            ['encuesta_id' => 5, 'candidato_id' => 8, 'porcentaje' => 3.00],
            ['encuesta_id' => 5, 'candidato_id' => 9, 'porcentaje' => 3.60],
            ['encuesta_id' => 5, 'candidato_id' => 10, 'porcentaje' => 1.30],
            ['encuesta_id' => 5, 'candidato_id' => 11, 'porcentaje' => 2.70],
            ['encuesta_id' => 5, 'candidato_id' => 12, 'porcentaje' => 0.25],
            ['encuesta_id' => 5, 'candidato_id' => 13, 'porcentaje' => 0.15],
        ];

        foreach ($resultados as $resultado) {
            EncuestaResultado::create($resultado);
        }

        // 5. NOTICIAS
        $noticias = [
            [
                'titulo' => 'Primera vuelta: Colombia decide este 31 de mayo',
                'contenido' => 'Más de 39 millones de colombianos están habilitados para votar en las elecciones presidenciales de primera vuelta este 31 de mayo de 2026. Los puestos de votación abrirán desde las 8:00 a.m. hasta las 4:00 p.m. La Registraduría Nacional ha dispuesto más de 112,000 mesas de votación en todo el territorio nacional.',
                'resumen' => 'Colombia se prepara para las elecciones presidenciales más competidas de la última década.',
                'fuente' => 'El Tiempo', 'autor' => 'Redacción Política', 'categoria' => 'politica', 'destacada' => true
            ],
            [
                'titulo' => 'Cepeda lidera encuestas pero sin mayoría para primera vuelta',
                'contenido' => 'Las últimas encuestas de intención de voto muestran a Iván Cepeda del Pacto Histórico liderando con aproximadamente 28% de intención de voto, seguido por Paloma Valencia del Centro Democrático con 18%. Sin embargo, analistas consideran que una segunda vuelta es prácticamente inevitable dado que ningún candidato supera el 50%.',
                'resumen' => 'Iván Cepeda lidera las encuestas pero una segunda vuelta parece inevitable.',
                'fuente' => 'Semana', 'autor' => 'Andrea Torres', 'categoria' => 'encuestas', 'destacada' => true
            ],
            [
                'titulo' => 'Debate presidencial: candidatos chocan por modelo económico',
                'contenido' => 'En el último debate presidencial organizado por la Comisión Nacional de Debates, los 13 candidatos presentaron visiones contrapuestas sobre el modelo económico. Mientras Cepeda propone mayor intervención estatal, Valencia y De la Espriella defienden la economía de mercado. Fajardo y López buscan un punto medio con énfasis en innovación.',
                'resumen' => 'Candidatos presentaron propuestas económicas divergentes en debate televisado.',
                'fuente' => 'RCN Noticias', 'autor' => 'Felipe Arias', 'categoria' => 'debate', 'destacada' => true
            ],
            [
                'titulo' => 'Redes sociales: el nuevo campo de batalla electoral',
                'contenido' => 'TikTok e Instagram se han convertido en las plataformas principales de campaña para las elecciones 2026. Abelardo de la Espriella lidera en engagement con más de 2 millones de interacciones semanales, mientras Cepeda domina en Twitter/X con su base militante. La inversión en publicidad digital supera los $50,000 millones.',
                'resumen' => 'Las redes sociales definen la campaña electoral de 2026.',
                'fuente' => 'La Silla Vacía', 'autor' => 'Juanita León', 'categoria' => 'politica', 'destacada' => false
            ],
            [
                'titulo' => 'Registraduría: todo listo para las elecciones del 31 de mayo',
                'contenido' => 'El registrador nacional Alexander Vega confirmó que la logística electoral está al 100%. Se desplegaron más de 300,000 jurados de votación y se reforzó la seguridad con presencia de Fuerzas Armadas en todo el territorio. El conteo de votos comenzará inmediatamente después del cierre de mesas.',
                'resumen' => 'La logística electoral está completamente preparada para la jornada.',
                'fuente' => 'Caracol Radio', 'autor' => 'Darío Arizmendi', 'categoria' => 'politica', 'destacada' => false
            ],
            [
                'titulo' => 'Economía electoral: ¿qué proponen los candidatos?',
                'contenido' => 'Los candidatos presidenciales han presentado planes económicos que van desde la reforma tributaria progresiva de Cepeda, pasando por la reducción de impuestos de Valencia, hasta el modelo de economía naranja actualizado de Lizcano. Los gremios empresariales han manifestado preocupación por propuestas que consideran populistas.',
                'resumen' => 'Análisis de las propuestas económicas de los 13 candidatos.',
                'fuente' => 'Portafolio', 'autor' => 'Economía Hoy', 'categoria' => 'economia', 'destacada' => false
            ],
            [
                'titulo' => 'MOE alerta sobre riesgos electorales en 5 departamentos',
                'contenido' => 'La Misión de Observación Electoral (MOE) identificó riesgos de fraude y violencia electoral en Cauca, Norte de Santander, Arauca, Chocó y Putumayo. La organización desplegó más de 5,000 observadores para garantizar la transparencia del proceso electoral.',
                'resumen' => 'La MOE identificó departamentos con alto riesgo electoral.',
                'fuente' => 'El Espectador', 'autor' => 'Redacción Judicial', 'categoria' => 'politica', 'destacada' => true
            ],
            [
                'titulo' => 'Encuesta revela que 35% de votantes aún no decide su voto',
                'contenido' => 'Según la última medición del Centro Nacional de Consultoría, un 35% de los colombianos habilitados para votar aún no ha definido su candidato. Este grupo de indecisos podría ser determinante para definir quién pasa a segunda vuelta y quién será el próximo presidente.',
                'resumen' => 'Un tercio de los votantes colombianos permanece indeciso antes de la primera vuelta.',
                'fuente' => 'Noticias Caracol', 'autor' => 'Mónica Jaramillo', 'categoria' => 'encuestas', 'destacada' => true
            ],
            [
                'titulo' => 'Candidatos firman pacto de transparencia y aceptación de resultados',
                'contenido' => 'Los 13 candidatos presidenciales firmaron un pacto de transparencia en el que se comprometen a aceptar los resultados electorales y a mantener un clima de paz durante y después de la jornada electoral. El acto fue acompañado por la comunidad internacional y la ONU.',
                'resumen' => 'Candidatos se comprometen a respetar los resultados electorales.',
                'fuente' => 'BluRadio', 'autor' => 'Néstor Morales', 'categoria' => 'politica', 'destacada' => false
            ],
            [
                'titulo' => 'Voto joven: los millennials y centennials definirían la elección',
                'contenido' => 'Por primera vez en la historia de Colombia, los votantes entre 18 y 35 años representan más del 40% del censo electoral. Las campañas de Claudia López y Abelardo de la Espriella han captado la mayor atención de este segmento, según estudios de redes sociales y grupos focales realizados en universidades.',
                'resumen' => 'El voto joven será determinante en las elecciones de 2026.',
                'fuente' => 'Revista Cambio', 'autor' => 'Santiago Cruz', 'categoria' => 'social', 'destacada' => false
            ]
        ];

        foreach ($noticias as $noticia) {
            Noticia::create($noticia);
        }

        // 6. TENDENCIAS REDES
        $tendencias = [
            // Twitter
            ['candidato_id' => 1, 'plataforma' => 'twitter', 'menciones' => 185000, 'sentimiento' => 68.50, 'seguidores' => 2100000, 'interacciones' => 890000, 'hashtag_principal' => '#CepedaPresidente', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 2, 'plataforma' => 'twitter', 'menciones' => 142000, 'sentimiento' => 62.30, 'seguidores' => 1800000, 'interacciones' => 720000, 'hashtag_principal' => '#PalomaPresidenta', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 3, 'plataforma' => 'twitter', 'menciones' => 168000, 'sentimiento' => 55.80, 'seguidores' => 1500000, 'interacciones' => 950000, 'hashtag_principal' => '#AbelardoPresidente', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 4, 'plataforma' => 'twitter', 'menciones' => 78000, 'sentimiento' => 71.20, 'seguidores' => 980000, 'interacciones' => 320000, 'hashtag_principal' => '#FajardoPresidente', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 5, 'plataforma' => 'twitter', 'menciones' => 95000, 'sentimiento' => 65.40, 'seguidores' => 1200000, 'interacciones' => 480000, 'hashtag_principal' => '#ClaudiaPresidenta', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 6, 'plataforma' => 'twitter', 'menciones' => 45000, 'sentimiento' => 48.90, 'seguidores' => 650000, 'interacciones' => 180000, 'hashtag_principal' => '#RoyPresidente', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 7, 'plataforma' => 'twitter', 'menciones' => 38000, 'sentimiento' => 52.30, 'seguidores' => 420000, 'interacciones' => 150000, 'hashtag_principal' => '#CaicedoPresidente', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 8, 'plataforma' => 'twitter', 'menciones' => 32000, 'sentimiento' => 58.10, 'seguidores' => 380000, 'interacciones' => 120000, 'hashtag_principal' => '#MurilloPresidente', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 9, 'plataforma' => 'twitter', 'menciones' => 42000, 'sentimiento' => 55.60, 'seguidores' => 520000, 'interacciones' => 190000, 'hashtag_principal' => '#LizcanoPresidente', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 10, 'plataforma' => 'twitter', 'menciones' => 15000, 'sentimiento' => 45.20, 'seguidores' => 180000, 'interacciones' => 55000, 'hashtag_principal' => '#BoteroPresidente', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 11, 'plataforma' => 'twitter', 'menciones' => 52000, 'sentimiento' => 50.80, 'seguidores' => 580000, 'interacciones' => 210000, 'hashtag_principal' => '#UribePresidente', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 12, 'plataforma' => 'twitter', 'menciones' => 8000, 'sentimiento' => 42.50, 'seguidores' => 95000, 'interacciones' => 25000, 'hashtag_principal' => '#SondraPresidenta', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 13, 'plataforma' => 'twitter', 'menciones' => 5000, 'sentimiento' => 40.10, 'seguidores' => 65000, 'interacciones' => 18000, 'hashtag_principal' => '#MatamorosPresidente', 'fecha_registro' => '2026-05-27'],

            // TikTok
            ['candidato_id' => 1, 'plataforma' => 'tiktok', 'menciones' => 220000, 'sentimiento' => 72.10, 'seguidores' => 1800000, 'interacciones' => 1500000, 'hashtag_principal' => '#Cepeda2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 2, 'plataforma' => 'tiktok', 'menciones' => 180000, 'sentimiento' => 65.80, 'seguidores' => 1400000, 'interacciones' => 1200000, 'hashtag_principal' => '#Paloma2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 3, 'plataforma' => 'tiktok', 'menciones' => 310000, 'sentimiento' => 58.40, 'seguidores' => 2500000, 'interacciones' => 2100000, 'hashtag_principal' => '#Abelardo2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 4, 'plataforma' => 'tiktok', 'menciones' => 65000, 'sentimiento' => 68.90, 'seguidores' => 450000, 'interacciones' => 280000, 'hashtag_principal' => '#Fajardo2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 5, 'plataforma' => 'tiktok', 'menciones' => 145000, 'sentimiento' => 70.20, 'seguidores' => 1100000, 'interacciones' => 850000, 'hashtag_principal' => '#Claudia2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 6, 'plataforma' => 'tiktok', 'menciones' => 28000, 'sentimiento' => 45.30, 'seguidores' => 220000, 'interacciones' => 95000, 'hashtag_principal' => '#Roy2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 7, 'plataforma' => 'tiktok', 'menciones' => 22000, 'sentimiento' => 50.10, 'seguidores' => 180000, 'interacciones' => 78000, 'hashtag_principal' => '#Caicedo2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 8, 'plataforma' => 'tiktok', 'menciones' => 18000, 'sentimiento' => 55.60, 'seguidores' => 150000, 'interacciones' => 62000, 'hashtag_principal' => '#Murillo2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 9, 'plataforma' => 'tiktok', 'menciones' => 35000, 'sentimiento' => 52.80, 'seguidores' => 280000, 'interacciones' => 120000, 'hashtag_principal' => '#Lizcano2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 10, 'plataforma' => 'tiktok', 'menciones' => 12000, 'sentimiento' => 48.50, 'seguidores' => 95000, 'interacciones' => 42000, 'hashtag_principal' => '#Botero2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 11, 'plataforma' => 'tiktok', 'menciones' => 48000, 'sentimiento' => 54.20, 'seguidores' => 350000, 'interacciones' => 180000, 'hashtag_principal' => '#MiguelUribe2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 12, 'plataforma' => 'tiktok', 'menciones' => 5000, 'sentimiento' => 40.80, 'seguidores' => 42000, 'interacciones' => 15000, 'hashtag_principal' => '#Sondra2026', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 13, 'plataforma' => 'tiktok', 'menciones' => 3000, 'sentimiento' => 38.50, 'seguidores' => 28000, 'interacciones' => 9000, 'hashtag_principal' => '#Matamoros2026', 'fecha_registro' => '2026-05-27'],

            // Instagram
            ['candidato_id' => 1, 'plataforma' => 'instagram', 'menciones' => 125000, 'sentimiento' => 70.30, 'seguidores' => 1600000, 'interacciones' => 680000, 'hashtag_principal' => '#CepedaColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 2, 'plataforma' => 'instagram', 'menciones' => 110000, 'sentimiento' => 67.50, 'seguidores' => 1300000, 'interacciones' => 580000, 'hashtag_principal' => '#PalomaColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 3, 'plataforma' => 'instagram', 'menciones' => 195000, 'sentimiento' => 60.20, 'seguidores' => 1900000, 'interacciones' => 920000, 'hashtag_principal' => '#AbelardoColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 4, 'plataforma' => 'instagram', 'menciones' => 55000, 'sentimiento' => 72.80, 'seguidores' => 680000, 'interacciones' => 250000, 'hashtag_principal' => '#FajardoColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 5, 'plataforma' => 'instagram', 'menciones' => 98000, 'sentimiento' => 68.90, 'seguidores' => 950000, 'interacciones' => 420000, 'hashtag_principal' => '#ClaudiaColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 6, 'plataforma' => 'instagram', 'menciones' => 32000, 'sentimiento' => 50.40, 'seguidores' => 380000, 'interacciones' => 130000, 'hashtag_principal' => '#RoyColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 7, 'plataforma' => 'instagram', 'menciones' => 25000, 'sentimiento' => 53.60, 'seguidores' => 280000, 'interacciones' => 95000, 'hashtag_principal' => '#CaicedoColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 8, 'plataforma' => 'instagram', 'menciones' => 20000, 'sentimiento' => 57.20, 'seguidores' => 250000, 'interacciones' => 82000, 'hashtag_principal' => '#MurilloColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 9, 'plataforma' => 'instagram', 'menciones' => 30000, 'sentimiento' => 54.90, 'seguidores' => 350000, 'interacciones' => 140000, 'hashtag_principal' => '#LizcanoColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 10, 'plataforma' => 'instagram', 'menciones' => 10000, 'sentimiento' => 46.30, 'seguidores' => 120000, 'interacciones' => 38000, 'hashtag_principal' => '#BoteroColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 11, 'plataforma' => 'instagram', 'menciones' => 38000, 'sentimiento' => 52.10, 'seguidores' => 420000, 'interacciones' => 160000, 'hashtag_principal' => '#UribeColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 12, 'plataforma' => 'instagram', 'menciones' => 6000, 'sentimiento' => 43.80, 'seguidores' => 65000, 'interacciones' => 20000, 'hashtag_principal' => '#SondraColombia', 'fecha_registro' => '2026-05-27'],
            ['candidato_id' => 13, 'plataforma' => 'instagram', 'menciones' => 4000, 'sentimiento' => 41.20, 'seguidores' => 45000, 'interacciones' => 12000, 'hashtag_principal' => '#MatamorosColombia', 'fecha_registro' => '2026-05-27'],
        ];

        foreach ($tendencias as $tendencia) {
            TendenciaRedes::create($tendencia);
        }

        // 7. COMENTARIOS
        $comentarios = [
            ['usuario_id' => 2, 'contenido' => '¡Estas elecciones están muy reñidas! Espero que gane el mejor candidato para Colombia. 🇨🇴'],
            ['usuario_id' => 3, 'contenido' => 'Es fundamental que todos salgamos a votar. La democracia se construye con participación.'],
            ['usuario_id' => 4, 'contenido' => 'Las encuestas dicen una cosa pero el voto real puede sorprender. ¡A votar!'],
            ['usuario_id' => 5, 'contenido' => 'Colombia necesita un cambio real, no más de lo mismo. Vamos con toda este 31 de mayo.'],
            ['usuario_id' => 6, 'contenido' => 'Que las elecciones sean en paz y que se respeten los resultados, sea quien sea el ganador.'],
        ];

        foreach ($comentarios as $comentario) {
            Comentario::create($comentario);
        }

        // 8. VOTOS
        $votos = [
            ['usuario_id' => 2, 'candidato_id' => 1], // María votó por Cepeda
            ['usuario_id' => 3, 'candidato_id' => 2], // Carlos votó por Paloma
            ['usuario_id' => 4, 'candidato_id' => 5], // Ana votó por Claudia López
            ['usuario_id' => 5, 'candidato_id' => 3], // Juan votó por Abelardo
            ['usuario_id' => 6, 'candidato_id' => 4], // Laura votó por Fajardo
        ];

        foreach ($votos as $voto) {
            Voto::create($voto);
        }
    }
}
