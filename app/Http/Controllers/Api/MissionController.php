<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Hero;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MissionController extends Controller
{
    /**
     * Listar todas las misiones.
     */
    //region Listar
    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $missions = Mission::paginate(10, ['*'], 'page', $page);
        return response()->json($missions, 200);
    }

    /**
     * Consultar una misión por ID.
     */
    public function show(string $id): JsonResponse
    {
        $mission = Mission::find($id);

        if (!$mission) {
            return response()->json([
                'mensaje' => 'Misión no encontrada'
            ], 404);
        }

        return response()->json($mission, 200);
    }

    /**
     * Consultar por palabra clave
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->query('keyword', '');

        $missions = Mission::where('title_mission', 'LIKE', "%$keyword%")
            ->orWhere('description_mission', 'LIKE', "%$keyword%")
            ->paginate(10);

        if ($missions->isEmpty()) {
            return response()->json([
                'mensaje' => 'No se encontraron misiones que coincidan con la palabra clave'
            ], 404);
        }

        return response()->json($missions, 200);
    }

    /**
     * Crear una nueva misión.
     */
    //region Crear
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(
                [
                    'title_mission' => 'required|string|max:255',
                    'description_mission' => 'nullable|string',
                    'difficulty_mission' => 'required|in:easy,medium,hard,extreme,yes',
                    'status_mission' => 'required|in:starting,pending,in_progress,completed,cancelled,failed',
                ],
                $this->messages()
            );

            $mission = Mission::create($validated);

            return response()->json([
                'message' => 'Misión creada exitosamente',
                'data' => $mission
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }



    /**
     * Actualizar una misión.
     */
    //region Actualizar
    public function update(Request $request, string $id): JsonResponse
    {
        $mission = Mission::find($id);

        if (!$mission) {
            return response()->json([
                'message' => 'Misión no encontrada'
            ], 404);
        }

        try {
            $validated = $request->validate(
                [
                    'title_mission' => 'sometimes|required|string|max:255',
                    'description_mission' => 'nullable|string',
                    'difficulty_mission' => 'sometimes|required|in:easy,medium,hard,extreme,yes',
                    'status_mission' => 'sometimes|required|in:starting,pending,in_progress,completed,cancelled,failed',
                ],
                $this->messages()
            );

            $mission->update($validated);

            return response()->json([
                'message' => 'Misión actualizada exitosamente',
                'data' => $mission
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Eliminar una misión.
     */
    //region Eliminar
    public function destroy(string $id): JsonResponse
    {
        $mission = Mission::find($id);

        if (!$mission) {
            return response()->json([
                'message' => 'Misión no encontrada'
            ], 404);
        }

        $mission->delete();

        return response()->json([
            'message' => 'Misión eliminada exitosamente'
        ], 200);
    }

    /**
     * Obtener todos los héroes asignados a una misión.
     */
    //region Héroes
    public function heroes(string $id): JsonResponse
    {
        $mission = Mission::find($id);

        if (!$mission) {
            return response()->json([
                'message' => 'Misión no encontrada'
            ], 404);
        }

        $heroes = $mission->heroes;

        return response()->json([
            'message' => 'Héroes de la misión obtenidos exitosamente',
            'data' => [
                'mission' => $mission,
                'heroes' => $heroes
            ]
        ], 200);
    }

    /**
     * Asignar héroes a una misión con datos adicionales (grupo, notas).
     */
    //region Asignar héroes
    public function assignHeroes(Request $request, string $id): JsonResponse
    {
        $mission = Mission::find($id);

        if (!$mission) {
            return response()->json([
                'message' => 'Misión no encontrada'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'heroes' => 'required|array',
                'heroes.*.hero_id' => 'required|exists:heroes,id_hero',
                'heroes.*.group_name' => 'nullable|string|max:255',
                'heroes.*.notes' => 'nullable|string'
            ], [
                'heroes.required' => 'Debe proporcionar al menos un héroe.',
                'heroes.array' => 'Los héroes deben ser un array.',
                'heroes.*.hero_id.required' => 'Cada héroe debe tener un ID.',
                'heroes.*.hero_id.exists' => 'Uno o más héroes no existen.'
            ]);

        // Preparar datos para sincronización con campos pivote
        $syncData = [];

        foreach ($validated['heroes'] as $hero) {
            $hero['hero_id'];
            $modelhero = Hero::find($hero['hero_id']);
            $heroname = $modelhero->name_hero;
            if ($heroname == 'Krisda' || $heroname == 'Krisda2'){
                $modelhero2 = Hero::where('name_hero', 'Krisda')->orWhere('name_hero', 'Krisda2');
                $numrows = $modelhero2->count();
                //Asignamos a los 'Krisda' heroes a la mision ya que no sabemos de quien esten hablando
                foreach ($modelhero2->get() as $heroindb){
                    $syncData[$heroindb['id_hero']] = [
                        'status' => 'assigned',
                        'group_name' => $hero['group_name'] ?? null,
                        'notes' => $hero['notes'] ?? null,
                        'started_at' => null,
                        'completed_at' => null,
                        'failed_at' => null
                    ];
                }
            }else{
                //Menos mal no es Krisda, asignamos normalmente
                $syncData[$hero['hero_id']] = [
                    'status' => 'assigned',
                    'group_name' => $hero['group_name'] ?? null,
                    'notes' => $hero['notes'] ?? null,
                    'started_at' => null,
                    'completed_at' => null,
                    'failed_at' => null
                ];
            }
        }

            $mission->heroes()->sync($syncData);

            return response()->json([
                'message' => 'Héroes asignados exitosamente a la misión',
                'data' => [
                    'mission' => $mission,
                    'heroes' => $mission->heroes
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
     * Consultar el estado de un grupo en una misión.
     */
    public function getGroupStatus(string $missionId, string $groupName): JsonResponse
    {
        $mission = Mission::find($missionId);

        if (!$mission) {
            return response()->json([
                'message' => 'Misión no encontrada'
            ], 404);
        }

        // Obtener todos los héroes del grupo en esta misión
        $heroesInGroup = $mission->heroes()
            ->wherePivot('group_name', $groupName)
            ->get();

        if ($heroesInGroup->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron héroes en el grupo especificado para esta misión'
            ], 404);
        }

        return response()->json([
            'message' => 'Estado del grupo obtenido exitosamente',
            'data' => [
                'mission' => $mission,
                'group_name' => $groupName,
                'total_heroes' => $heroesInGroup->count(),
                'heroes' => $heroesInGroup
            ]
        ], 200);
    }

    /**
     * Actualizar el estado de todos los héroes de un grupo en una misión.
     */
    public function updateGroupStatus(Request $request, string $missionId): JsonResponse
    {
        $mission = Mission::find($missionId);

        if (!$mission) {
            return response()->json([
                'message' => 'Misión no encontrada'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'group_name' => 'required|string|max:255',
                'status' => 'required|in:assigned,in_progress,completed,failed',
                'notes' => 'nullable|string'
            ], [
                'group_name.required' => 'El nombre del grupo es obligatorio.',
                'status.required' => 'El estado es obligatorio.',
                'status.in' => 'El estado debe ser: assigned, in_progress, completed o failed.'
            ]);

        // Obtener todos los héroes del grupo en esta misión
        $heroesInGroup = $mission->heroes()
            ->wherePivot('group_name', $validated['group_name'])
            ->get();

        if ($heroesInGroup->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron héroes en el grupo especificado para esta misión'
            ], 404);
        }

        // Preparar datos de actualización
        $updateData = ['status' => $validated['status']];
        
        if (isset($validated['notes'])) {
            $updateData['notes'] = $validated['notes'];
        }

        // Actualizar timestamps según el estado
        if ($validated['status'] === 'in_progress') {
            $updateData['started_at'] = now();
        } elseif ($validated['status'] === 'completed') {
            $updateData['completed_at'] = now();
        } elseif ($validated['status'] === 'failed') {
            $updateData['failed_at'] = now();
        }

        // Actualizar cada héroe del grupo
        foreach ($heroesInGroup as $hero) {
            $mission->heroes()->updateExistingPivot($hero->id_hero, $updateData);
        }

        // Recargar la relación para obtener datos actualizados
        $mission->load('heroes');
        $updatedHeroes = $mission->heroes()
            ->wherePivot('group_name', $validated['group_name'])
            ->get();

            return response()->json([
                'message' => 'Estado del grupo actualizado exitosamente',
                'data' => [
                    'mission' => $mission,
                    'group_name' => $validated['group_name'],
                    'heroes_updated' => $updatedHeroes->count(),
                    'heroes' => $updatedHeroes
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
     * Eliminar un grupo completo de una misión (desasignar todos los héroes del grupo).
     */
    public function deleteGroup(string $missionId, string $groupName): JsonResponse
    {
        $mission = Mission::find($missionId);

        if (!$mission) {
            return response()->json([
                'message' => 'Misión no encontrada'
            ], 404);
        }

        // Obtener todos los héroes del grupo en esta misión
        $heroesInGroup = $mission->heroes()
            ->wherePivot('group_name', $groupName)
            ->get();

        if ($heroesInGroup->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron héroes en el grupo especificado para esta misión'
            ], 404);
        }

        $heroCount = $heroesInGroup->count();
        $heroIds = $heroesInGroup->pluck('id_hero')->toArray();

        // Desasignar (eliminar de la tabla pivote) todos los héroes del grupo
        $mission->heroes()->detach($heroIds);

        return response()->json([
            'message' => 'Grupo eliminado exitosamente de la misión',
            'data' => [
                'mission_id' => $mission->id_mission,
                'group_name' => $groupName,
                'heroes_removed' => $heroCount
            ]
        ], 200);
    }

    /**
     * Mensajes de validación en español.
     */
    //region Mensajes
    private function messages(): array
    {
        return [
            'title_mission.required' => 'El título de la misión es obligatorio.',
            'title_mission.string' => 'El título de la misión debe ser una cadena de texto.',
            'title_mission.max' => 'El título de la misión no debe exceder los 255 caracteres.',
            'description_mission.string' => 'La descripción de la misión debe ser una cadena de texto.',
            'difficulty_mission.required' => 'La dificultad de la misión es obligatoria.',
            'difficulty_mission.in' => 'La dificultad de la misión debe ser: easy, medium, hard, extreme o yes.',
            'status_mission.required' => 'El estado de la misión es obligatorio.',
            'status_mission.in' => 'El estado de la misión debe ser: starting, pending, in_progress, completed, cancelled o failed.',
        ];
    }
}