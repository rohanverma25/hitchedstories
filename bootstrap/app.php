<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(
            fn (Request $request) => $request->is('admin', 'admin/*') ? route('admin.login') : '/'
        );

        $middleware->redirectUsersTo(
            fn (Request $request) => $request->is('admin', 'admin/*') ? route('admin.dashboard') : '/'
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // An upload bigger than PHP's post_max_size arrives with an empty body;
        // send the admin back to the form with a message instead of a bare 413 page.
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            if ($request->is('admin/*')) {
                return back()->withErrors([
                    'upload' => 'The upload was too large. The server accepts up to '.ini_get('post_max_size').' per upload; try fewer or smaller files.',
                ]);
            }
        });
    })->create();
