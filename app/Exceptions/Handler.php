<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use ApiResponse;
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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
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
        if($exception instanceof ValidationException)
        {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if($exception instanceof ModelNotFoundException)
        {
            $model = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("No existe ninguna instancia de ${model} con el id especificado", 404);
        }

        if($exception instanceof AuthenticationException)
        {
            return $this->unauthenticated($request, $exception);
        }

        if($exception instanceof AuthorizationException)
        {
            return $this->errorResponse('No posee permisos para ejecutar esta acción', 403);
        }

        if($exception instanceof NotFoundHttpException)
        {
            return $this->errorResponse('No se encontró la URL especificada: ' . $request->path(), 403);
        }

        if($exception instanceof MethodNotAllowedHttpException)
        {
            return $this->errorResponse('El método especificado en la petición no es válido', 405);
        }

        if($exception instanceof HttpException)
        {
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        if($exception instanceof QueryException)
        {
            $codigo = $exception->errorInfo[1];
            if($codigo == 1451)
            {
                return $this->errorResponse('No se puede eliminar de forma permanente el recurso porque está relacionado con algún otro recurso', 409);
            }
        }

        if(config('app.debug'))
        {
            return parent::render($request, $exception);
        }
        return $this->errorResponse('Falla inesperada. Inténtelo más tarde.', 500);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse('No autenticado', 401);
    }
}
