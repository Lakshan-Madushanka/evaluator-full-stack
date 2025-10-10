<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

        if (\request()->wantsJson()) {
            $this->renderable(function (NotFoundHttpException $exception) {
                $message = $exception->getMessage();
                if (! empty($message) && str_contains($message, 'No query results')) {
                    return new JsonResponse('Resource not found', 404);
                }
            });

            /**
             * Handle integrity constraint violation when try to delete record which has
             *foreign key associated with other record
             */
            $this->renderable(function (QueryException $exception, Request $request) {
                if (isset($exception->errorInfo[1]) && $exception->errorInfo[1] === 1451) {
                    return new JsonResponse('The Record  has associated with other records !',
                        400);
                }
            });
        }
    }
}
