<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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

        if($exception instanceof AuthenticationException){
            return errorResponse('you are not authenticated. Please login.', 401);
        }
        if ($exception instanceof UnauthorizedException) {
            return errorResponse('You do not have required authorization.', 403);
        }
        if($exception instanceof ValidationException){
            return errorResponse(array_values($exception->errors())[0]??'',422,$exception->validator->errors()->all());
        }
        if($exception instanceof ModelNotFoundException){
            return errorResponse($exception->getMessage(), 404);
        }
        if ($exception instanceof NotFoundHttpException) {
            return errorResponse('The specified URL cannot be found.', 404);
        }
        if ($exception instanceof AuthorizationException) {
            return errorResponse($exception->getMessage(), 403);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return errorResponse($exception->getMessage(), 405);
        }
        return errorResponse($exception->getMessage(), 500, config('app.debug') ? $exception->getTrace() : []);
    }
}
