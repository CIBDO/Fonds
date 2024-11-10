<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {
        // Gestion de l'erreur 404 (Page non trouvée)
        if ($exception instanceof HttpException && $exception->getStatusCode() == 404) {
            return response()->view('errors.404', [], 404);
        }

        // Gestion de l'erreur 403 (Accès interdit)
        if ($exception instanceof HttpException && $exception->getStatusCode() == 403) {
            return response()->view('errors.403', [], 403);
        }

        // Gestion de l'erreur 500 (Erreur serveur)
        if ($exception instanceof HttpException && $exception->getStatusCode() == 500) {
            return response()->view('errors.500', [], 500);
        }

        // Autres exceptions générales
        return parent::render($request, $exception);
    }
}
