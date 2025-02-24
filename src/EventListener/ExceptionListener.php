<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\DBAL\Exception as DoctrineException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\RuntimeException as SecurityRuntimeException;
use Symfony\Component\ErrorHandler\Error\UndefinedMethodError;

final class ExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            $response = new JsonResponse(
                [
                    'code' => Response::HTTP_NOT_FOUND,
                    'error' => 'Not Found.'
                ], Response::HTTP_NOT_FOUND
            );

            $event->setResponse($response);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            $response = new JsonResponse(
                [
                    'code' => Response::HTTP_METHOD_NOT_ALLOWED,
                    'message' => 'Method Not Allowed.'
                ], Response::HTTP_METHOD_NOT_ALLOWED
            );

            $event->setResponse($response);
        }

        if ($exception instanceof UndefinedMethodError) {
            $response = new JsonResponse(
                [
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'Undefined Method.'
                ], Response::HTTP_INTERNAL_SERVER_ERROR
            );

            $event->setResponse($response);
        }

        if ($exception instanceof BadRequestException) {
            $response = new JsonResponse(
                [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Bad Request.'
                ], Response::HTTP_BAD_REQUEST
            );

            $event->setResponse($response);
        }

        if ($exception instanceof DoctrineException) {
            $response = new JsonResponse(
                [
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'Data Base Exception.'
                ], Response::HTTP_INTERNAL_SERVER_ERROR
            );

            $event->setResponse($response);
        }

        if ($exception instanceof UnexpectedValueException) {
            $response = new JsonResponse(
                [
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'Unexpected Value.'
                ], Response::HTTP_INTERNAL_SERVER_ERROR
            );

            $event->setResponse($response);
        }

        if ($exception instanceof SecurityRuntimeException) {
            $response = new JsonResponse(
                [
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'Security Runtime Exception.'
                ], Response::HTTP_INTERNAL_SERVER_ERROR
            );

            $event->setResponse($response);
        }

        if (
            $exception instanceof AccessDeniedHttpException
            || $exception instanceof AccessDeniedException
        ) {
            $response = new JsonResponse(
                [
                    'code' => Response::HTTP_FORBIDDEN,
                    'message' => 'Access denied to this resource.'
                ], Response::HTTP_FORBIDDEN
            );

            $event->setResponse($response);
        }

        if ($exception instanceof ValidationFailedException) {
            $violations = $exception->getViolations();
            $errors = [];

            foreach ($violations as $violation) {
                $errors[] = [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                    'invalidValue' => $violation->getInvalidValue(),
                ];
            }

            $response = new JsonResponse(
                [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'errors' => $errors
                ], Response::HTTP_BAD_REQUEST
            );

            $event->setResponse($response);
        }
    }
}
