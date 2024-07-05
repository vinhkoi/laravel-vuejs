<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    }
    public function render($request, Throwable $exception)
    {
        // Kiểm tra các ngoại lệ cần xử lý
        if ($exception instanceof QueryException || $exception instanceof NotFoundHttpException) {
            return Inertia::render('NotFound', [], 404); // Sử dụng Inertia để trả về trang Vue.js
        }

        // Đối với các ngoại lệ khác, sử dụng render mặc định
        return parent::render($request, $exception);
    }
}
