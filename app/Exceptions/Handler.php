<?php

namespace App\Exceptions;

use App\Http\Requests\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
        $this->reportable(function (Throwable $exception) {

        });
    }

    public function render($request, Throwable $e)
    {

        if ($e instanceof AuthorizationException) {
            return response()->json(['status' => 403, 'message' => $e->getMessage()], 403);
        } else if ($e instanceof ModelNotFoundException) {
            return response()->json(['status' => 404 ,'message' => 'Entry for '.str_replace('App\\Models\\', '', $e->getModel()).' not found'], 404);
        } else if ($e instanceof AuthenticationException) {
            return response()->json(['status' => 401, 'message' => $e->getMessage()], 401);
        }

        return parent::render($request, $e);
    }

}
