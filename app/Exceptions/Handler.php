<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Log\Writer;
use App\Traits\ApiResponse;
use Psr\Log\LoggerInterface;
use App\Traits\NeedJsonResponse;
use App\Libraries\HoiAjaxCall as Call;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

    use ApiResponse;
    use NeedJsonResponse;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception){
//        parent::report($exception);

        /** @var Writer $logger */
        $logger = $this->container->make(LoggerInterface::class);
        $logger->useDailyFiles(storage_path('logs/error.log'), 0);

        $message = $exception->getMessage();
        $file    = $exception->getFile();
        $line    = $exception->getLine();

        $logger->error($message, [
            'file|line' => "$file|$line"
        ]);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception){
        if($this->needJsonResponse($request)){
            $code = 422;
            $msg = Call::SERVER_THROWN_EXCEPTION;
            $data = [];
            $errorMsg = $exception->getMessage();

            return $this->errorResponse($data, $code, $msg, $errorMsg);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception){
        if ($this->needJsonResponse($request)) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
