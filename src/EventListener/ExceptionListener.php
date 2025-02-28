<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Response\ApiErrorResponse;
use Doctrine\DBAL\Exception as DoctrineException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Security\Core\Exception\RuntimeException as SecurityRuntimeException;
use Symfony\Component\ErrorHandler\Error\UndefinedMethodError;

final class ExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = $this->makeResponse($exception);
        $event->setResponse($response);
    }

    protected function makeResponse(\Throwable $exception): JsonResponse
    {
        $errors = null;
        $statusCode = ($exception instanceof HttpExceptionInterface)
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        switch (true) {
            case $exception instanceof \DomainException:
                $message = 'Business logic error.';
                break;

            case $exception instanceof \InvalidArgumentException:
                $message = 'Invalid input data.';
                $statusCode = Response::HTTP_NOT_ACCEPTABLE;
                break;

            case ($exception instanceof NotFoundHttpException):
                $message = 'Not Found.';
                $statusCode = Response::HTTP_NOT_FOUND;
                break;

            case ($exception instanceof MethodNotAllowedHttpException):
                $message = 'Method Not Allowed.';
                $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;
                break;

            case ($exception instanceof UndefinedMethodError):
                $message = 'Undefined Method.';
                break;

            case ($exception instanceof BadRequestException):
                $message = 'Bad Request.';
                $statusCode = Response::HTTP_BAD_REQUEST;
                break;

            case ($exception instanceof DoctrineException):
                $message = 'Data Base Exception.';
                break;

            case ($exception instanceof UnexpectedValueException):
                $message = 'Unexpected Value.';
                break;

            case ($exception instanceof SecurityRuntimeException):
                $message = 'Security Runtime Exception.';
                break;

            case ($exception instanceof ValidationFailedException):
                $violations = $exception->getViolations();

                foreach ($violations as $violation) {
                    $errors[] = [
                        'field' => $violation->getPropertyPath(),
                        'violationMessage' => $violation->getMessage(),
                        'invalidValue' => $violation->getInvalidValue(),
                    ];
                }

                $message = 'Validation exception.';
                $statusCode = Response::HTTP_BAD_REQUEST;
                break;

            case ($exception instanceof AccessDeniedHttpException):
                $message = 'Access denied to this resource.';
                $statusCode = Response::HTTP_FORBIDDEN;
                break;

            default:
                $message = $exception->getMessage();
                break;
        }

        return ApiErrorResponse::error($message, $statusCode, $errors);
    }
}
