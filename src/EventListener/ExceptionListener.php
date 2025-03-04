<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $flattened = FlattenException::createFromThrowable($exception);

        switch (true) {
            case ($exception instanceof ValidationFailedException):
                $errors = $this->validationException($exception);
                $flattened->setMessage('Validation exception.');
                $flattened->setStatusCode(Response::HTTP_BAD_REQUEST);
                break;
            case ($exception instanceof ExtraAttributesException):
                $flattened->setStatusCode(Response::HTTP_BAD_REQUEST);
                break;
        }


        $data = [
            'status' => Response::$statusTexts[$flattened->getStatusCode()],
            'code' => $flattened->getStatusCode(),
            'message' => $flattened->getMessage(),
            'errors' => $errors ?? null,
        ];

        $response = new JsonResponse($data, $flattened->getStatusCode());

        $event->setResponse($response);
    }

    private function validationException(ValidationFailedException $exception): array
    {
        $errors = null;
        $violations = $exception->getViolations();

        foreach ($violations as $violation) {
            $errors[] = [
                'field' => $violation->getPropertyPath(),
                'violationMessage' => $violation->getMessage(),
                'invalidValue' => $violation->getInvalidValue(),
            ];
        }
        return $errors;
    }
}
