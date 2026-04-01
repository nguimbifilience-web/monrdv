<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'clinic' => \App\Http\Middleware\EnsureUserBelongsToClinic::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);

        // Appliquer le middleware clinic à toutes les routes web authentifiées
       // $middleware->appendToGroup('web', \App\Http\Middleware\EnsureUserBelongsToClinic::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
