<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // handle authentication exception
        $this->renderable(function(\Illuminate\Auth\AuthenticationException $e, $request){
            if($request->is("api/*")) {
                return response()->json([
                    "message" => "Not authenticated"
                ], 401);
            }
        });

        // handle request validation exception
        $this->renderable(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Please check your from input',
                    'errors' => $e->errors()
                ], 422);
            }
        });

        // handle 404 api
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Not found'
                ], 404);
            }
        });

        // handle 5** api
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Something went wrong'
                ], $e->getStatusCode());
            }
        });
    }
}
