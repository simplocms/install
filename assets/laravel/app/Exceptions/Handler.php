<?php

namespace App\Exceptions;

use App\Structures\Enums\SingletonEnum;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class Handler extends ExceptionHandler
{
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
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        if ($this->shouldReport($exception) &&
            (app()->environment('production') || config('sentry.allow_in_development'))
        ) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $this->setLocaleOfException();

        if ($exception instanceof \Illuminate\Session\TokenMismatchException){
            if ($request->ajax()) {
                return response()->json([
                    'renew_token' => csrf_token()
                ], 401);
            }
            return redirect()->route('admin.auth.login');
        }

        return parent::render($request, $exception);
    }

    /**
     * Render the given HttpException. Here we make sure, that if theme context contains
     * method "renderErrorException", we handle the exception to this method render returned view.
     *
     * @param  \Symfony\Component\HttpKernel\Exception\HttpException  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpException $e)
    {
        try {
            $context = SingletonEnum::theme()->getContextInstance();
            if (method_exists($context, 'renderErrorException')) {
                $view = $context->renderErrorException($e);

                if ($view instanceof Response) {
                    return $view;
                }

                if ($view !== null) {
                    return response()->make($view, $e->getStatusCode(), $e->getHeaders());
                }
            }
        } catch (\Throwable $exception) {
            // do nothing
        }

        return parent::renderHttpException($e);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    /**
     * Set locale of rendered exception.
     */
    private function setLocaleOfException(): void
    {
        try {
            $language = SingletonEnum::languagesCollection()->getContentLanguage();
            app()->setLocale($language->language_code);
        } catch (\Throwable $exception) {
            // do nothing
        }
    }
}
