#!/bin/bash

# Script de prueba para validar el manejo de errores en JSON
# Este script demuestra cómo probar los diferentes tipos de errores

echo "=================================="
echo "Pruebas de Manejo de Errores JSON"
echo "=================================="
echo ""

BASE_URL="http://localhost/api"

echo "1. Probar error de validación (422)"
echo "-----------------------------------"
echo "POST $BASE_URL/heroes con datos vacíos"
echo ""
echo "curl -X POST $BASE_URL/heroes \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -H 'Accept: application/json' \\"
echo "  -d '{}'"
echo ""
echo "Respuesta esperada:"
echo '{'
echo '  "message": "Error de validación",'
echo '  "status": 422,'
echo '  "errors": {'
echo '    "name_hero": ["El nombre del héroe es obligatorio."],'
echo '    "race_hero": ["La raza del héroe es obligatoria."],'
echo '    "role_hero": ["El rol del héroe es obligatorio."]'
echo '  }'
echo '}'
echo ""

echo "2. Probar recurso no encontrado (404)"
echo "--------------------------------------"
echo "GET $BASE_URL/heroes/99999"
echo ""
echo "curl -X GET $BASE_URL/heroes/99999 \\"
echo "  -H 'Accept: application/json'"
echo ""
echo "Respuesta esperada:"
echo '{'
echo '  "mensaje": "Héroe no encontrado"'
echo '}'
echo ""

echo "3. Probar método no permitido (405)"
echo "------------------------------------"
echo "PATCH $BASE_URL/heroes (método no soportado para listado)"
echo ""
echo "curl -X PATCH $BASE_URL/heroes \\"
echo "  -H 'Accept: application/json'"
echo ""
echo "Respuesta esperada:"
echo '{'
echo '  "message": "Método no permitido",'
echo '  "status": 405'
echo '}'
echo ""

echo "4. Probar error de base de datos (503)"
echo "---------------------------------------"
echo "Para simular este error:"
echo "1. Detener el servicio de PostgreSQL:"
echo "   docker compose stop pgsql"
echo ""
echo "2. Intentar acceder a cualquier endpoint:"
echo "   curl -X GET $BASE_URL/heroes -H 'Accept: application/json'"
echo ""
echo "Respuesta esperada:"
echo '{'
echo '  "message": "Error de conexión a la base de datos",'
echo '  "status": 503'
echo '}'
echo ""
echo "3. Reiniciar PostgreSQL:"
echo "   docker compose start pgsql"
echo ""

echo "5. Probar ruta no existente (404)"
echo "----------------------------------"
echo "GET $BASE_URL/nonexistent"
echo ""
echo "curl -X GET $BASE_URL/nonexistent \\"
echo "  -H 'Accept: application/json'"
echo ""
echo "Respuesta esperada:"
echo '{'
echo '  "message": "Recurso no encontrado",'
echo '  "status": 404'
echo '}'
echo ""

echo "=================================="
echo "IMPORTANTE:"
echo "=================================="
echo ""
echo "Todos los errores ahora retornan JSON con:"
echo "- 'message': Descripción del error en español"
echo "- 'status': Código HTTP apropiado"
echo "- 'errors': Detalles adicionales (solo en modo debug)"
echo ""
echo "Para ejecutar las pruebas reales:"
echo "1. Asegurar que el servidor esté corriendo:"
echo "   docker compose up -d"
echo ""
echo "2. Ejecutar cada comando curl mostrado arriba"
echo ""
echo "3. Verificar que todas las respuestas sean JSON válido"
echo ""
