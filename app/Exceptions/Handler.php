<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Laravel\Lumen\Application;
use Throwable;
use App\Response\ApiResponse;
use App\Response\ErrorCode;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use JsonSchema\Exception\ResourceNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return JsonResponse|Response|View|Application
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*') || $request->is('oauth/*')) {
            if ($exception instanceof NotFoundHttpException) {
                return ApiResponse::error(ErrorCode::NOT_FOUND, $exception->getMessage());
            }

            if ($exception instanceof AuthenticationException) {
                return ApiResponse::error(ErrorCode::UNAUTHORIZED, $exception->getMessage());
            }

            if ($exception instanceof ApiErrorException) {
                return ApiResponse::error($exception->getCode(), $exception->getMessage());
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return ApiResponse::error(ErrorCode::ALLOW_NOT_METHOD, $exception->getMessage());
            }

            return ApiResponse::error(ErrorCode::SERVER_ERROR, $exception->getMessage());
        }

        if($request->is('api/posts/*') || $request->is('posts/*')) {
            if ($exception instanceof NotFoundHttpException || $exception instanceof ResourceNotFoundException) {
                return view('404');
            }
        }

        if($request->is('infographics/*')){
            if ($exception instanceof NotFoundHttpException || $exception instanceof ResourceNotFoundException) {
                return view('sb-admin.404');
            } else{
                return view('sb-admin.500');
            }
        }

        return parent::render($request, $exception);
    }
}
