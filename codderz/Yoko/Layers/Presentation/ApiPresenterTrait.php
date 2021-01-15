<?php

namespace Codderz\Yoko\Layers\Presentation;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

trait ApiPresenterTrait
{
    public function successApiResponse($payload, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'payload' => $payload
        ], $code);
    }

    public function errorApiResponse($message = null, $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'payload' => null
        ], $code);
    }

    public function handleApiException($request, Throwable $exception)
    {
        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorApiResponse('The specified method for the request is invalid', 405);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->errorApiResponse('The specified URL cannot be found', 404);
        }

        if ($exception instanceof HttpException) {
            return $this->errorApiResponse($exception->getMessage(), $exception->getStatusCode());
        }

        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        return $this->errorApiResponse('Unexpected Exception. Try later', 500);
    }
}
