<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
        ]);
        $middleware->api(prepend: [
            SetLocale::class,
        ]);
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'locale' => SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $resolveLocale = function (Request $request): void {
            if ($user = $request->user()) {
                if (! empty($user->locale) && in_array($user->locale, ['id', 'en'], true)) {
                    app()->setLocale($user->locale);
                    return;
                }
            }
            if ($header = $request->header('Accept-Language')) {
                foreach (explode(',', $header) as $part) {
                    $code = substr(strtolower(trim(explode(';', $part)[0])), 0, 2);
                    if (in_array($code, ['id', 'en'], true)) {
                        app()->setLocale($code);
                        return;
                    }
                }
            }
        };

        // Standardized JSON response for unauthenticated API requests (HTTP 401)
        $exceptions->render(function (AuthenticationException $e, Request $request) use ($resolveLocale) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $resolveLocale($request);
                return response()->json([
                    'message' => __('api.unauthenticated'),
                    'errors' => null,
                ], 401);
            }
        });

        // Standardized JSON response for unauthorized policy/gate violations (HTTP 403)
        $exceptions->render(function (AuthorizationException|AccessDeniedHttpException $e, Request $request) use ($resolveLocale) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $resolveLocale($request);
                $message = $e->getMessage();
                if (empty($message) || $message === 'This action is unauthorized.') {
                    $message = __('api.forbidden');
                }

                return response()->json([
                    'message' => $message,
                    'errors' => null,
                ], 403);
            }
        });

        // Standardized JSON response for missing model or route (HTTP 404)
        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e, Request $request) use ($resolveLocale) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $resolveLocale($request);
                return response()->json([
                    'message' => __('api.not_found'),
                    'errors' => null,
                ], 404);
            }
        });

        // Standardized JSON response for Domain HttpExceptions (e.g. 422 Invalid Transitions)
        $exceptions->render(function (HttpException $e, Request $request) use ($resolveLocale) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $resolveLocale($request);
                return response()->json([
                    'message' => $e->getMessage() ?: 'An HTTP error occurred.',
                    'errors' => null,
                ], $e->getStatusCode());
            }
        });
    })->create();
