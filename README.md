# 🗡️ API REST - Misiones en la Tierra Media

API REST desarrollada con **Laravel 12** y **PostgreSQL** para gestionar héroes y misiones, con soporte para asignación de grupos, seguimiento de estado individual y timestamps de progreso.

## 🚀 Características

- ✅ CRUD completo de Héroes y Misiones
- ✅ Asignación de héroes a misiones (many-to-many)
- ✅ Sistema de grupos para misiones en equipo
- ✅ Seguimiento individual de estado por héroe (assigned, in_progress, completed, failed)
- ✅ Timestamps automáticos (started_at, completed_at, failed_at)
- ✅ Búsqueda y filtrado con paginación (10 registros por página)
- ✅ Validaciones en español
- ✅ Dockerizado con Laravel Sail
- ✅ Reglas especiales para héroes legendarios (Rediqui, Krisda, Mixart, Ernie)

## 📋 Requisitos

- **Docker Desktop** (Windows/Mac/Linux)
- **Git**

## 🛠️ Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/Rediqui/API-REST-Laravel---Misiones-en-la-Tierra-Media.git
cd API-REST-Laravel---Misiones-en-la-Tierra-Media
```

### 2. Configurar el entorno

```bash
cp .env.example .env
```

### 3. Instalar dependencias PHP

**En Windows:**
```bash
docker run --rm ^
    -v "%cd%":/var/www/html ^
    -w /var/www/html ^
    laravelsail/php84-composer:latest ^
    composer install --ignore-platform-reqs
```

**En Linux/Mac:**
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

### 4. Iniciar contenedores Docker

```bash
docker compose up -d
```

### 5. Generar clave de aplicación y migrar base de datos

```bash
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate:fresh --seed
```

### 6. ¡Listo! 🎉

Accede a la API en: **http://localhost/api/heroes**

## 📚 Documentación de la API

Ver archivo **[API-DOCS.md](API-DOCS.md)** para:
- Todos los endpoints disponibles (20 rutas)
- Ejemplos de peticiones y respuestas
- Códigos de estado HTTP
- Flujos completos de trabajo

### Endpoints principales:

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/heroes` | Listar héroes (paginado) |
| POST | `/api/heroes` | Crear héroe |
| GET | `/api/heroes/{id}/missions` | Ver misiones de un héroe |
| POST | `/api/heroes/{id}/missions` | Asignar misiones a un héroe |
| PUT | `/api/heroes/{heroId}/missions/{missionId}` | Actualizar estado individual |
| GET | `/api/missions` | Listar misiones (paginado) |
| POST | `/api/missions/{id}/heroes` | Asignar héroes a una misión |
| GET | `/api/missions/{id}/groups/{groupName}` | Consultar estado de un grupo |
| PUT | `/api/missions/{id}/groups/status` | Actualizar estado de todo un grupo |
| DELETE | `/api/missions/{id}/groups/{groupName}` | Eliminar grupo de una misión |

## 🐳 Comandos Docker útiles

```bash
# Ver contenedores corriendo
docker compose ps

# Ver logs en tiempo real
docker compose logs -f laravel.test

# Ejecutar comandos artisan
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test php artisan tinker

# Detener contenedores
docker compose down

# Reiniciar todo (borra BD)
docker compose down -v
docker compose up -d
docker compose exec laravel.test php artisan migrate:fresh --seed

# Acceder al bash del contenedor
docker compose exec laravel.test bash
```

## 🗄️ Acceso a la base de datos

Puedes conectarte a PostgreSQL con cualquier cliente (DBeaver, pgAdmin, etc.):

- **Host:** `localhost`
- **Puerto:** `5432`
- **Database:** `examen_api`
- **Usuario:** `sail`
- **Password:** `password`

## 🎯 Datos de prueba

El seeder crea automáticamente:
- **7 héroes** con nombres, razas y roles variados
- **15 misiones** con diferentes dificultades y estados
- Incluye héroes especiales: Rediqui (indestructible), Krisda/Krisda2 (gemelos), Mixart, Ernie

## 🧪 Testing

```bash
docker compose exec laravel.test php artisan test
```

## 🤝 Reglas especiales

- **Rediqui**: No puede ser eliminado ni fallar misiones
- **Krisda/Krisda2**: Al asignar/eliminar uno, se afecta al otro
- **Mixart**: Requiere confirmación especial
- **Ernie**: Siempre falla automáticamente

## 📖 Tecnologías

- **Laravel 12** (PHP 8.4)
- **PostgreSQL 18**
- **Docker & Laravel Sail**
- **Composer**
- **Git**

## 📝 Estructura del proyecto

```
.
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── HeroController.php
│   │   └── MissionController.php
│   └── Models/
│       ├── Hero.php
│       └── Mission.php
├── database/
│   ├── factories/
│   ├── migrations/
│   │   ├── create_heroes_table.php
│   │   ├── create_missions_table.php
│   │   └── create_hero_mission_table.php
│   └── seeders/
├── routes/
│   └── api.php
├── compose.yaml
├── API-DOCS.md
└── README.md
```

## 📄 Licencia

Este proyecto es de código abierto bajo la licencia MIT.

---

Desarrollado con ❤️ por [Rediqui](https://github.com/Rediqui)
