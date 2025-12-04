# ========================================
# API REST - Misiones en la Tierra Media
# ========================================
# Base URL: http://localhost/api
# Paginación: 10 registros por página (parámetro: ?page=1)
# ========================================

# ==================== HÉROES ====================

# 1. LISTAR TODOS LOS HÉROES (paginado)
GET http://localhost/api/heroes
GET http://localhost/api/heroes?page=2

# 2. CONSULTAR HÉROE POR ID
GET http://localhost/api/heroes/1
GET http://localhost/api/heroes/{id}

# 3. BUSCAR HÉROES (paginado)
GET http://localhost/api/heroes/search/query?name_hero=Mixart
GET http://localhost/api/heroes/search/query?race_hero=Elfo
GET http://localhost/api/heroes/search/query?role_hero=Guerrero
GET http://localhost/api/heroes/search/query?name_hero=Rediqui&race_hero=Humano&page=1

# 4. CREAR NUEVO HÉROE
POST http://localhost/api/heroes
Content-Type: application/json
Accept: application/json

{
  "name_hero": "Jeysoon",
  "race_hero": "Humano",
  "role_hero": "Guerrero"
}

# 5. ACTUALIZAR HÉROE
PUT http://localhost/api/heroes/1
Content-Type: application/json
Accept: application/json

{
  "name_hero": "Krisda2",
  "race_hero": "Humano",
  "role_hero": "Sanador"
}

# También funciona con PATCH (actualización parcial)
PATCH http://localhost/api/heroes/1
Content-Type: application/json

{
  "role_hero": "Arquero"
}

# 6. ELIMINAR HÉROE
DELETE http://localhost/api/heroes/1
# ⚠️ Nota: Algunos héroes tienen reglas especiales:
# - "Rediqui": No puede ser eliminado (403) :<
# - "Krisda"/"Krisda2": Se eliminan ambos juntos
# - "Mixart": xd...
# - "Ernie": Elimina todos los Ernies

# 7. VER MISIONES DE UN HÉROE
GET http://localhost/api/heroes/1/missions
# Respuesta incluye datos del pivote (status, group_name, timestamps, notes)

# 8. ASIGNAR MISIONES A UN HÉROE
POST http://localhost/api/heroes/1/missions
Content-Type: application/json
Accept: application/json

{
  "missions": [
    {
      "mission_id": 1,
      "group_name": "Mixart Team",
      "notes": "Aqui va una nota"
    },
    {
      "mission_id": 5,
      "group_name": 'Equipo Mata Dioses',
      "notes": "Aqui va otra nota"
    }
  ]
}

# 9. ACTUALIZAR ESTADO DE UNA MISIÓN DE UN HÉROE
PUT http://localhost/api/heroes/1/missions/1
Content-Type: application/json
Accept: application/json

# Cambiar a "en progreso" (registra started_at automáticamente)
{
  "status": "in_progress",
  "notes": "Iniciando"
}

# Marcar como completada (registra completed_at)
{
  "status": "completed",
  "notes": "Comienza la travesia"
}

# Marcar como fallida (registra failed_at)
{
  "status": "failed",
  "notes": "Falto Vanderwolk"
}

# Estados disponibles: assigned, in_progress, completed, failed
# ⚠️ Nota: "Rediqui" nunca puede fallar misiones (403)
# ⚠️ Nota: "Ernie" siempre falla automáticamente (403)


# ==================== MISIONES ====================

# 1. LISTAR TODAS LAS MISIONES (paginado)
GET http://localhost/api/missions
GET http://localhost/api/missions?page=2

# 2. CONSULTAR MISIÓN POR ID
GET http://localhost/api/missions/1
GET http://localhost/api/missions/{id}

# 3. BUSCAR MISIONES (paginado)
GET http://localhost/api/missions/search/query?title_mission=Matar%20a%20Dios
GET http://localhost/api/missions/search/query?description_mission=Mordor
GET http://localhost/api/missions/search/query?difficulty_mission=extreme
GET http://localhost/api/missions/search/query?status_mission=pending
GET http://localhost/api/missions/search/query?title_mission=Destruir&difficulty_mission=extreme&page=1

# 4. CREAR NUEVA MISIÓN
POST http://localhost/api/missions
Content-Type: application/json
Accept: application/json

{
  "title_mission": "Rescatar a los hobbits",
  "description_mission": "Libera a Vander de las garras de Luna",
  "difficulty_mission": "hard",
  "status_mission": "pending"
}

# Dificultades disponibles: easy, medium, hard, extreme, yes
# Estados disponibles: starting, pending, in_progress, completed, cancelled, failed

# 5. ACTUALIZAR MISIÓN
PUT http://localhost/api/missions/1
Content-Type: application/json
Accept: application/json

{
  "title_mission": "Destruir el Anillo Único - Actualizado",
  "description_mission": "Nueva descripción",
  "difficulty_mission": "extreme",
  "status_mission": "completed"
}

# PATCH para actualización parcial
PATCH http://localhost/api/missions/1
Content-Type: application/json

{
  "status_mission": "in_progress"
}

# 6. ELIMINAR MISIÓN
DELETE http://localhost/api/missions/1

# 7. VER HÉROES DE UNA MISIÓN
GET http://localhost/api/missions/1/heroes
# Respuesta incluye datos del pivote (status, group_name, timestamps, notes)

# 8. ASIGNAR HÉROES A UNA MISIÓN
POST http://localhost/api/missions/1/heroes
Content-Type: application/json
Accept: application/json

{
  "heroes": [
    {
      "hero_id": 1,
      "group_name": "PolloEquipo",
      "notes": "Aragorn - Líder"
    },
    {
      "hero_id": 2,
      "group_name": "PolloEquipo",
      "notes": "Gandalf - Guía"
    },
    {
      "hero_id": 3,
      "group_name": "PolloEquipo",
      "notes": "Legolas - Arquero"
    },
    {
      "hero_id": 4,
      "group_name": "Grupo de Rescate",
      "notes": "Grupo separado"
    }
  ]
}

# 9. CONSULTAR ESTADO DE UN GRUPO EN UNA MISIÓN
GET http://localhost/api/missions/1/groups/PolloEquipo
# Nota: Si el nombre del grupo tiene espacios, codifícalos: 
# GET http://localhost/api/missions/1/groups/Equipo%20Alpha

# Respuesta:
# {
#   "message": "Estado del grupo obtenido exitosamente",
#   "data": {
#     "mission": {...},
#     "group_name": "PolloEquipo",
#     "total_heroes": 3,
#     "heroes": [
#       {
#         "id_hero": 1,
#         "name_hero": "Aragorn",
#         "pivot": {
#           "status": "in_progress",
#           "group_name": "PolloEquipo",
#           "started_at": "2025-11-29T14:00:00Z",
#           "notes": "Líder"
#         }
#       }
#     ]
#   }
# }

# 10. ACTUALIZAR ESTADO DE TODO UN GRUPO EN UNA MISIÓN
PUT http://localhost/api/missions/1/groups/status
Content-Type: application/json
Accept: application/json

# Actualizar todos los héroes del "PolloEquipo" a "in_progress"
{
  "group_name": "PolloEquipo",
  "status": "in_progress",
  "notes": "El equipo ha comenzado la misión"
}

# Marcar todo el grupo como completado
{
  "group_name": "PolloEquipo",
  "status": "completed",
  "notes": "Misión cumplida por el equipo completo"
}

# Respuesta incluye cuántos héroes fueron actualizados:
# {
#   "message": "Estado del grupo actualizado exitosamente",
#   "data": {
#     "mission": {...},
#     "group_name": "PolloEquipo",
#     "heroes_updated": 3,
#     "heroes": [...]
#   }
# }

# 11. ELIMINAR UN GRUPO COMPLETO DE UNA MISIÓN
DELETE http://localhost/api/missions/1/groups/PolloEquipo
# Desasigna todos los héroes del grupo de la misión (elimina registros de hero_mission)

# Respuesta:
# {
#   "message": "Grupo eliminado exitosamente de la misión",
#   "data": {
#     "mission_id": 1,
#     "group_name": "PolloEquipo",
#     "heroes_removed": 3
#   }
# }


# ==================== EJEMPLOS DE FLUJOS COMPLETOS ====================

# FLUJO 1: Crear héroe y asignarle misiones
# Paso 1: Crear héroe
POST http://localhost/api/heroes
{
  "name_hero": "Rediqui",
  "race_hero": "Humano",
  "role_hero": "Indrestructible"
}
# Respuesta: {"message": "...", "data": {"id_hero": 5, ...}}

# Paso 2: Asignar misiones al héroe recién creado
POST http://localhost/api/heroes/5/missions
{
  "missions": [
    {"mission_id": 1, "group_name": "Comunidad", "notes": "Portador principal"}
  ]
}

# Paso 3: Actualizar estado de la misión
PUT http://localhost/api/heroes/5/missions/1
{
  "status": "in_progress"
}

# Paso 4: Completar la misión
PUT http://localhost/api/heroes/5/missions/1
{
  "status": "completed",
  "notes": "El anillo ha sido destruido"
}

# FLUJO 2: Crear misión grupal con múltiples héroes
# Paso 1: Crear misión
POST http://localhost/api/missions
{
  "title_mission": "Defender a Terato",
  "description_mission": "Proteger el castillo",
  "difficulty_mission": "extreme",
  "status_mission": "pending"
}
# Respuesta: {"message": "...", "data": {"id_mission": 10, ...}}

# Paso 2: Asignar grupo de héroes
POST http://localhost/api/missions/10/heroes
{
  "heroes": [
    {"hero_id": 1, "group_name": "Defensores de terato", "notes": "Comandante"},
    {"hero_id": 2, "group_name": "Defensores de terato", "notes": "Mago de apoyo"},
    {"hero_id": 3, "group_name": "Defensores de terato", "notes": "Arquero Noviciano"}
  ]
}

# Paso 3: Ver progreso de la misión con todos los héroes
GET http://localhost/api/missions/10/heroes

# Paso 4: Actualizar estado individual de cada héroe
PUT http://localhost/api/heroes/1/missions/10
{"status": "in_progress"}

PUT http://localhost/api/heroes/2/missions/10
{"status": "completed"}

PUT http://localhost/api/heroes/3/missions/10
{"status": "completed"}

# FLUJO 3: Buscar y filtrar
# Buscar todos los héroes Elfos
GET http://localhost/api/heroes/search/query?race_hero=Elfo

# Buscar misiones difíciles pendientes
GET http://localhost/api/missions/search/query?difficulty_mission=extreme&status_mission=pending

# Buscar héroes guerreros en la página 2
GET http://localhost/api/heroes/search/query?role_hero=Guerrero&page=2


# ==================== RESPUESTAS DE VALIDACIÓN ====================

# Todos los mensajes de validación están en español:
# - "El nombre del héroe es obligatorio."
# - "La dificultad de la misión debe ser: easy, medium, hard, extreme o yes."
# - "El estado debe ser: assigned, in_progress, completed o failed."


# ==================== CÓDIGOS HTTP ====================

# 200 OK - Operación exitosa (GET, PUT, PATCH)
# 201 Created - Recurso creado exitosamente (POST)
# 404 Not Found - Recurso no encontrado
# 403 Forbidden - Operación no permitida (ej: eliminar "Rediqui")
# 422 Unprocessable Entity - Error de validación


# ==================== ESTRUCTURA DE RESPUESTA ====================

# Respuesta exitosa con datos:
{
  "message": "Héroe creado exitosamente",
  "data": {
    "id_hero": 1,
    "name_hero": "Aragorn",
    "race_hero": "Humano",
    "role_hero": "Espadachín",
    "created_at": "2025-11-29T12:00:00.000000Z",
    "updated_at": "2025-11-29T12:00:00.000000Z"
  }
}

# Respuesta paginada:
{
  "current_page": 1,
  "data": [...],
  "first_page_url": "http://localhost/api/heroes?page=1",
  "from": 1,
  "last_page": 2,
  "last_page_url": "http://localhost/api/heroes?page=2",
  "next_page_url": "http://localhost/api/heroes?page=2",
  "path": "http://localhost/api/heroes",
  "per_page": 10,
  "prev_page_url": null,
  "to": 10,
  "total": 15
}

# Respuesta con relaciones (hero->missions):
{
  "message": "Misiones del héroe obtenidas exitosamente",
  "data": {
    "hero": {...},
    "missions": [
      {
        "id_mission": 1,
        "title_mission": "Destruir el Anillo",
        "pivot": {
          "id_hero": 1,
          "id_mission": 1,
          "status": "in_progress",
          "group_name": "Comunidad del Anillo",
          "started_at": "2025-11-29T13:00:00.000000Z",
          "completed_at": null,
          "failed_at": null,
          "notes": "Líder del grupo",
          "created_at": "2025-11-29T12:00:00.000000Z",
          "updated_at": "2025-11-29T13:00:00.000000Z"
        }
      }
    ]
  }
}

# Error 404:
{
  "mensaje": "Héroe no encontrado"
}

# Error 403:
{
  "message": "No se puede a un heroe tan legendario"
}

# Error de validación:
{
  "message": "The given data was invalid.",
  "errors": {
    "name_hero": [
      "El nombre del héroe es obligatorio."
    ]
  }
}
