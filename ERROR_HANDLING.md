# Manejo de Errores en JSON

## Cambios Implementados

Se ha implementado un manejador global de excepciones para garantizar que **todos los errores** de la API sean retornados en formato JSON con sus códigos de error HTTP correspondientes.

### Ubicación del Cambio

El manejo de errores se configuró en: `bootstrap/app.php`

### Tipos de Errores Manejados

#### 1. Errores de Base de Datos (503)
Cuando hay problemas de conexión con la base de datos (QueryException o PDOException):

```json
{
  "message": "Error de conexión a la base de datos",
  "status": 503,
  "errors": {
    "error": "SQLSTATE[HY000] [2002] Connection refused",
    "sql": "SELECT * FROM heroes"
  }
}
```

**Nota:** Los detalles del error (`errors`) solo se muestran cuando `APP_DEBUG=true` en el archivo `.env`.

#### 2. Errores de Validación (422)
Ya manejados por los controladores, ahora también capturados globalmente. Los mensajes de validación están en español y son amigables para el usuario:

```json
{
  "message": "Error de validación",
  "status": 422,
  "errors": {
    "name_hero": [
      "El nombre del héroe es obligatorio."
    ]
  }
}
```

**Nota:** Los errores de validación siempre se muestran completos, ya que son mensajes diseñados para ser vistos por el usuario y ayudarle a corregir su entrada. No contienen información sensible del sistema.

#### 3. No Autenticado (401)

```json
{
  "message": "No autenticado",
  "status": 401
}
```

#### 4. No Autorizado (403)

```json
{
  "message": "No autorizado",
  "status": 403
}
```

#### 5. Recurso No Encontrado (404)
Aplica tanto para rutas no encontradas como para modelos no encontrados:

```json
{
  "message": "Recurso no encontrado",
  "status": 404
}
```

#### 6. Método No Permitido (405)

```json
{
  "message": "Método no permitido",
  "status": 405
}
```

#### 7. Demasiadas Solicitudes (429)
Cuando se excede el límite de tasa (rate limiting):

```json
{
  "message": "Demasiadas solicitudes. Por favor, intente más tarde.",
  "status": 429
}
```

#### 8. Error Interno del Servidor (500)
Para cualquier otro tipo de error no específicamente manejado:

```json
{
  "message": "Error interno del servidor",
  "status": 500
}
```

En modo debug (`APP_DEBUG=true`), incluye información adicional:

```json
{
  "message": "Division by zero",
  "status": 500,
  "errors": {
    "exception": "DivisionByZeroError",
    "file": "/path/to/file.php",
    "line": 42,
    "trace": [...]
  }
}
```

## Cómo Probar

### 1. Simular Error de Base de Datos

Detener el contenedor de PostgreSQL o cambiar las credenciales en `.env`:

```bash
# Detener PostgreSQL
docker compose stop pgsql

# Intentar acceder a cualquier endpoint
curl http://localhost/api/heroes
```

**Respuesta esperada:**
```json
{
  "message": "Error de conexión a la base de datos",
  "status": 503
}
```

### 2. Recurso No Encontrado

```bash
curl http://localhost/api/heroes/99999
```

**Respuesta esperada:**
```json
{
  "mensaje": "Héroe no encontrado",
  "status": 404
}
```

### 3. Error de Validación

```bash
curl -X POST http://localhost/api/heroes \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Respuesta esperada:**
```json
{
  "message": "Error de validación",
  "status": 422,
  "errors": {
    "name_hero": ["El nombre del héroe es obligatorio."],
    "race_hero": ["La raza del héroe es obligatoria."],
    "role_hero": ["El rol del héroe es obligatorio."]
  }
}
```

### 4. Método No Permitido

```bash
curl -X PATCH http://localhost/api/heroes
```

**Respuesta esperada:**
```json
{
  "message": "Método no permitido",
  "status": 405
}
```

## Ventajas de la Implementación

1. ✅ **Consistencia**: Todos los errores retornan JSON, no HTML
2. ✅ **Códigos HTTP apropiados**: Cada tipo de error tiene su código correcto
3. ✅ **Información de debug**: En desarrollo se muestran detalles útiles
4. ✅ **Seguridad**: En producción no se exponen detalles internos
5. ✅ **Compatible con APIs**: Facilita la integración con clientes frontend
6. ✅ **Mantenible**: Centralizado en un solo lugar (`bootstrap/app.php`)

## Comportamiento Anterior vs. Nuevo

### Antes
- Errores de base de datos retornaban HTML genérico de Laravel
- Excepciones no manejadas mostraban páginas de error completas
- Inconsistencia en el formato de respuestas

### Ahora
- **Todos** los errores retornan JSON estructurado
- Códigos HTTP apropiados para cada tipo de error
- Formato consistente para consumo por APIs REST

## Configuración de Producción

Para producción, asegúrate de configurar en `.env`:

```env
APP_ENV=production
APP_DEBUG=false
```

Esto ocultará los detalles técnicos de los errores y solo mostrará mensajes genéricos al usuario.
