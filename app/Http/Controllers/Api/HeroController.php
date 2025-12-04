<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HeroController extends Controller
{
    /**
     * Listar todos los héroes.
     */
    //region Listar
    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $heroes = Hero::paginate(10, ['*'], 'page', $page);
        return response()->json($heroes, 200);
    }

    /**
     * Consultar un héroe por ID.
     */
    public function show(string $id): JsonResponse
    {
        $hero = Hero::find($id);

        if (!$hero) {
            return response()->json([
                'mensaje' => 'Héroe no encontrado'
            ], 404);
        }

        return response()->json($hero, 200);
    }

    /**
     * Consultar por nombre, raza o rol.
     */
    public function search(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $query = Hero::query();

        if ($request->has('name_hero')) {
            $query->where('name_hero', 'like', '%' . $request->query('name_hero') . '%');
        }

        if ($request->has('race_hero')) {
            $query->where('race_hero', 'like', '%' . $request->query('race_hero') . '%');
        }

        if ($request->has('role_hero')) {
            $query->where('role_hero', 'like', '%' . $request->query('role_hero') . '%');
        }

        $heroes = $query->paginate(10, ['*'], 'page', $page);

        if ($heroes->isEmpty()) {
            return response()->json([
                'mensaje' => 'No se encontraron héroes que coincidan con los criterios de búsqueda'
            ], 404);
        }

        return response()->json($heroes, 200);
    }
        
    /**
     * Crear un nuevo héroe.
     */
    //region Crear
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(
                [
                    'name_hero' => 'required|string|max:255',
                    'race_hero' => 'required|string|max:255',
                    'role_hero' => 'required|string|max:255',
                ],
                $this->messages()
            );

            $hero = Hero::create($validated);

            return response()->json([
                'message' => 'Héroe creado exitosamente',
                'data' => $hero
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Actualizar un héroe.
     */
    //region Actualizar
    public function update(Request $request, string $id): JsonResponse
    {
        $hero = Hero::find($id);

        if (!$hero) {
            return response()->json([
                'mensaje' => 'Héroe no encontrado'
            ], 404);
        }

        try {
            $validated = $request->validate(
                [
                    'name_hero' => 'sometimes|required|string|max:255',
                    'race_hero' => 'sometimes|required|string|max:255',
                    'role_hero' => 'sometimes|required|string|max:255',
                ],
                $this->messages()
            );

            $hero->update($validated);

            return response()->json([
                'message' => 'Héroe actualizado exitosamente',
                'data' => $hero
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Eliminar un héroe.
     */
    //region Eliminar
    public function destroy(string $id): JsonResponse
    {
        $hero = Hero::find($id);

        $heromodel = new Hero();


        if (!$hero) {
            return response()->json([
                'message' => 'Héroe no encontrado'
            ], 404);
        }

        $hero = $heromodel->find($id);
        $heroname = $hero['name_hero'];

        if ($heroname == 'Rediqui'){
            return response()->json([
                'message' => 'No se puede a un heroe tan legendario'
            ], 403);
        }else if ($heroname == 'Krisda' || $heroname == 'Krisda2' || $heroname == 'Pollo' || $heroname == 'Pollo2'){
            return response()->json([
                'message' => 'No se sabe a quien se debe eliminar... Eliminando a ambos heroes es la mejor opcion'
            ], 403);

            $heromodel->where('name_hero', 'Krisda')->orWhere('name_hero', 'Krisda2')->delete();
        }else if ($heroname == 'Mixart'){
            return response()->json([
                'message' => 'Igual ni ocupamos un frontend'
            ], 403);
            $hero->delete();
        }else if ($heroname == 'Ernie'){
            return response()->json([
                'message' => 'Mejor eliminemos a todos los Ernies'
            ], 403);
            $heromodel->where('name_hero', 'Ernie')->delete();
        }else{
            $hero->delete();

        }

        return response()->json([
            'message' => 'Héroe eliminado exitosamente'
        ], 200);
    }

    /**
     * Obtener todas las misiones asignadas a un héroe.
     */
    //region Misiones
    public function missions(string $id): JsonResponse
    {
        $hero = Hero::find($id);

        if (!$hero) {
            return response()->json([
                'message' => 'Héroe no encontrado'
            ], 404);
        }

        $missions = $hero->missions;

        return response()->json([
            'message' => 'Misiones del héroe obtenidas exitosamente',
            'data' => [
                'hero' => $hero,
                'missions' => $missions
            ]
        ], 200);
    }

    /**
     * Asignar misiones a un héroe con datos adicionales (grupo, notas).
     */
    //region Asignar misiones
    public function assignMissions(Request $request, string $id): JsonResponse
    {
        $hero = Hero::find($id);

        if (!$hero) {
            return response()->json([
                'message' => 'Héroe no encontrado'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'missions' => 'required|array',
                'missions.*.mission_id' => 'required|exists:missions,id_mission',
                'missions.*.group_name' => 'nullable|string|max:255',
                'missions.*.notes' => 'nullable|string'
            ], [
                'missions.required' => 'Debe proporcionar al menos una misión.',
                'missions.array' => 'Las misiones deben ser un array.',
                'missions.*.mission_id.required' => 'Cada misión debe tener un ID.',
                'missions.*.mission_id.exists' => 'Una o más misiones no existen.'
            ]);

        // Preparar datos para sincronización con campos pivote
        $syncData = [];
        foreach ($validated['missions'] as $mission) {
            $syncData[$mission['mission_id']] = [
                'status' => 'assigned',
                'group_name' => $mission['group_name'] ?? null,
                'notes' => $mission['notes'] ?? null,
                'started_at' => null,
                'completed_at' => null,
                'failed_at' => null
            ];
        }

            $hero->missions()->sync($syncData);

            return response()->json([
                'message' => 'Misiones asignadas exitosamente al héroe',
                'data' => [
                    'hero' => $hero,
                    'missions' => $hero->missions
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Actualizar el estado de una misión para un héroe específico.
     */
    //region Actualizar estado de misión
    public function updateMissionStatus(Request $request, string $heroId, string $missionId): JsonResponse
    {
        $hero = Hero::find($heroId);

        if (!$hero) {
            return response()->json([
                'message' => 'Héroe no encontrado'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'status' => 'required|in:assigned,in_progress,completed,failed',
                'notes' => 'nullable|string'
            ], [
                'status.required' => 'El estado es obligatorio.',
                'status.in' => 'El estado debe ser: assigned, in_progress, completed o failed.'
            ]);

        // Verificar que la misión esté asignada al héroe
        if (!$hero->missions()->where('id_mission', $missionId)->exists()) {
            return response()->json([
                'message' => 'Esta misión no está asignada al héroe'
            ], 404);
        }

        // Preparar datos de actualización
        $updateData = ['status' => $validated['status']];
        
        if (isset($validated['notes'])) {
            $updateData['notes'] = $validated['notes'];
        }

        $modelhero = new Hero();
        $modelhero = $modelhero->find($heroId);
        $namehero = $modelhero['name_hero'];

        if ($namehero == 'Rediqui' && $validated['status'] == 'failed'){
            return response()->json([
                'message' => 'Rediqui nunca falla sus misiones'
            ], 403);
            $updateData['status'] = 'completed';
        }else if ($namehero == 'Ernie'){
            return response()->json([
                'message' => 'Mision fallida'
            ], 403);
            $updateData['status'] = 'failed';
        }

        // Actualizar timestamps según el estado
        if ($validated['status'] === 'in_progress') {
            $updateData['started_at'] = now();
        } elseif ($validated['status'] === 'completed') {
            $updateData['completed_at'] = now();
        } elseif ($validated['status'] === 'failed') {
            $updateData['failed_at'] = now();
        }

        // Actualizar el registro pivote
        $hero->missions()->updateExistingPivot($missionId, $updateData);

        // Obtener la misión actualizada con datos del pivote
        $mission = $hero->missions()->where('id_mission', $missionId)->first();

            return response()->json([
                'message' => 'Estado de la misión actualizado exitosamente',
                'data' => [
                    'hero' => $hero,
                    'mission' => $mission,
                    'pivot' => $mission->pivot
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Mensajes de validación en español.
     */
    //region Mensajes
    private function messages(): array
    {
        return [
            'name_hero.required' => 'El nombre del héroe es obligatorio.',
            'name_hero.string' => 'El nombre del héroe debe ser una cadena de texto.',
            'name_hero.max' => 'El nombre del héroe no debe exceder los 255 caracteres.',
            'race_hero.required' => 'La raza del héroe es obligatoria.',
            'race_hero.string' => 'La raza del héroe debe ser una cadena de texto.',
            'race_hero.max' => 'La raza del héroe no debe exceder los 255 caracteres.',
            'role_hero.required' => 'El rol del héroe es obligatorio.',
            'role_hero.string' => 'El rol del héroe debe ser una cadena de texto.',
            'role_hero.max' => 'El rol del héroe no debe exceder los 255 caracteres.',
        ];
    }
}