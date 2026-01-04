<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Ensure all exceptions return JSON responses for API requests
        $exceptions->render(function (\Throwable $e, $request) {
            // Always return JSON for API routes
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = 500;
                $message = 'Error interno del servidor';
                $errors = null;

                // Handle specific exception types
                if ($e instanceof \Illuminate\Database\QueryException) {
                    $statusCode = 503;
                    $message = 'Error de conexión a la base de datos';
                    $errors = config('app.debug') ? [
                        'error' => $e->getMessage(),
                        'sql' => $e->getSql() ?? null,
                    ] : null;
                } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                    $statusCode = 422;
                    $message = 'Error de validación';
                    $errors = $e->errors();
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    $statusCode = 404;
                    $message = 'Recurso no encontrado';
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    $statusCode = 405;
                    $message = 'Método no permitido';
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    $statusCode = $e->getStatusCode();
                    $message = $e->getMessage() ?: 'Error en la petición';
                } elseif (method_exists($e, 'getStatusCode')) {
                    $statusCode = $e->getStatusCode();
                    $message = $e->getMessage() ?: 'Error en la petición';
                } else {
                    $message = config('app.debug') ? $e->getMessage() : 'Error interno del servidor';
                    $errors = config('app.debug') ? [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => collect($e->getTrace())->take(5)->toArray()
                    ] : null;
                }

                $response = [
                    'message' => $message,
                    'status' => $statusCode
                ];

                if ($errors !== null) {
                    $response['errors'] = $errors;
                }

                return response()->json($response, $statusCode);
            }
        });
    })->create();