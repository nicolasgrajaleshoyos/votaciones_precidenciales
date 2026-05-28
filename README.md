# 🗳️ Elecciones Presidenciales Colombia 2026

Plataforma interactiva de predicción, votaciones y análisis estadístico en tiempo real para las elecciones presidenciales de Colombia 2026. Este sistema permite a los ciudadanos simular su voto, consultar las últimas encuestas, informarse con noticias relevantes y analizar tendencias de opinión pública digital de cara a la primera vuelta del 31 de mayo de 2026.

---

## 🚀 Arquitectura y Tecnologías

La aplicación está construida siguiendo las mejores prácticas modernas de desarrollo web:

- **Backend**: [Laravel 11/12](https://laravel.com/) - API REST segura estructurada en controladores de PHP.
- **Base de Datos**: [SQLite](https://sqlite.org/) - Almacenamiento rápido y autocontenido para el censo electoral y registros.
- **Frontend**: Single Page Application (SPA) construida con HTML5 Semántico, CSS3 con diseño personalizado (Glassmorphism + Dark Mode) y Javascript Vanilla (ES6 Modules).
- **Gráficos Estadísticos**: [Chart.js](https://www.chartjs.org/) - Visualización interactiva y responsiva de votos y tendencias en redes sociales.
- **Autenticación**: Laravel Sanctum (Tokens de API) e Integración con **Google Identity Services (OAuth)**.

---

## 🌟 Características Principales

### 1. Iniciar Sesión / Registro
- Registro convencional seguro de votantes con cédula de ciudadanía colombiana, departamento y municipio.
- **Acceso rápido con Google**: Autenticación directa a través del SDK oficial de Google que lee tus cuentas activas del navegador.

### 2. Simulación de Voto Real
- Módulo interactivo de votación donde los usuarios pueden emitir un voto secreto por su candidato preferido de forma definitiva (restricción estricta de un voto por usuario).

### 3. Estadísticas y Gráficos Interactivos
- **Resultados de Votación**: Histograma de votación acumulada en tiempo real.
- **Tendencias en Redes**: Seguimiento del pulso digital analizando la cantidad de menciones de los candidatos en plataformas clave (Twitter/X, Instagram, TikTok).

### 4. Candidatos Presidenciales
- Perfil detallado de los 13 candidatos inscritos en la primera vuelta electoral, incluyendo sus fórmulas vicepresidenciales, biografías completas y principales propuestas de campaña.

### 5. Encuestas de Opinión
- Repositorio histórico de las encuestas realizadas por las principales firmas del país (Invamer, Datexco, CNC, Guarumo) con sus respectivos desgloses, muestras estadísticas y márgenes de error.

### 6. Portal de Noticias
- Módulo informativo categorizado y filtrable por temas (Política, Encuestas, Debates, Economía, Social).

### 7. Panel de Administración (Admin Panel)
- Panel de control avanzado para supervisar el proceso:
  - Visualización del censo de usuarios y control de estado (activo/inactivo).
  - Monitoreo del registro general de votos por correo y fecha.
  - Gestión completa de creación/eliminación de noticias y encuestas del sistema.
  - Moderación y eliminación de comentarios en tiempo real.

---

## 🛠️ Instalación y Configuración Local

### Requisitos Previos
- **PHP 8.2** o superior
- **Composer**
- **Node.js y npm** (o Bun)

### Pasos de Configuración:

1. **Clonar el proyecto** y situarse en la carpeta raíz:
   ```bash
   git clone https://github.com/nicolasgrajaleshoyos/votaciones_precidenciales.git
   cd elecciones
   ```

2. **Instalar dependencias del Backend (PHP)**:
   ```bash
   composer install
   ```

3. **Configurar el archivo de entorno**:
   Duplica el archivo de ejemplo `.env.example` y nómbralo `.env`:
   ```bash
   cp .env.example .env
   ```
   *Asegúrate de que la conexión de base de datos sea SQLite:*
   ```env
   DB_CONNECTION=sqlite
   ```

4. **Generar la clave de la aplicación**:
   ```bash
   php artisan key:generate
   ```

5. **Preparar y Sembrar la Base de Datos**:
   Crea y puebla la base de datos SQLite con los datos iniciales de candidatos, encuestas de mayo de 2026, noticias, administradores y tendencias en redes sociales:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Configurar Google Login en tu entorno**:
   Abre tu archivo `.env` y añade tu clave de cliente OAuth de Google para habilitar el login:
   ```env
   GOOGLE_CLIENT_ID=TU_CLIENT_ID_REAL.apps.googleusercontent.com
   ```
   *(Asegúrate de que tu Client ID de Google tenga la URI `http://localhost:3000` agregada a los Orígenes de JavaScript Autorizados)*.

7. **Ejecutar el Servidor**:
   Inicia el servidor local de Laravel en el puerto 3000:
   ```bash
   php artisan serve --port=3000
   ```

8. Abre tu navegador e ingresa a: **`http://localhost:3000`**

---

## 🔒 Credenciales de Prueba (Administrador)
Para acceder al panel de administración del sistema y realizar pruebas de gestión, utiliza los siguientes datos de acceso:
- **Correo**: `admin@elecciones2026.co`
- **Contraseña**: `Admin2026!`

---

## 🌍 Despliegue en Internet (Producción Gratuita)

### Usando Render
1. Sube este repositorio a tu cuenta de GitHub.
2. Crea un **Web Service** nuevo en [Render.com](https://render.com/).
3. Configura:
   - **Environment**: `PHP`
   - **Build Command**: `composer install --no-dev && npm install && npm run build`
   - **Start Command**: `php artisan migrate --force && apache2-foreground`
4. Añade las variables de entorno en la sección Advanced: `APP_KEY`, `DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`, `GOOGLE_CLIENT_ID` y `APP_ENV=production`.

### Usando Fly.io (Recomendado para SQLite persistente)
1. Instala el CLI de Fly.io.
2. Ejecuta `fly launch` y sigue las instrucciones para crear el volumen del disco donde persistirá la base de datos `database.sqlite`.
3. Ejecuta `fly deploy`.
