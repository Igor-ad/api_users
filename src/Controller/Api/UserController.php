<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Enum\Roles;
use App\Enum\UserMessage;
use App\Repository\UserRepository;
use App\Resource\UserRequestDto;
use App\Security\Voter\UserVoter;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/v1/api/users', name: 'api_users_')]
class UserController extends BaseController
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected UserService         $service,
        protected UserRepository      $repository,
    ) {
        parent::__construct($this->serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted(Roles::Admin->value)]
    public function getUsers(): JsonResponse
    {
        $resource = $this->repository->findAll();

        return $this->jsonResponse(
            $resource,
            UserMessage::ALL,
            Response::HTTP_OK,
            ['user:public', 'user:detail']
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[IsGranted(UserVoter::VIEW, subject: 'user')]
    public function show(User $user): JsonResponse
    {
        return $this->jsonResponse([$user], UserMessage::SHOW);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[IsGranted(UserVoter::EDIT, subject: 'user')]
    public function update(User $user, #[MapRequestPayload] UserRequestDto $userRequestDto): JsonResponse {
        $mapper = $this->service->update($user, $userRequestDto);

        return $this->jsonResponse(
            $mapper,
            UserMessage::UPDATE,
            Response::HTTP_OK,
            ['user:detail'],
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] UserRequestDto $userRequestDto): JsonResponse
    {
        $mapper = $this->service->create($userRequestDto);

        return $this->jsonResponse(
            $mapper,
            UserMessage::CREATE,
            Response::HTTP_CREATED,
            ['user:detail', 'user:public'],
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted(Roles::Admin->value)]
    public function delete(User $user): JsonResponse
    {
        $this->repository->remove($user, true);

        return $this->jsonResponse(['success' => true], UserMessage::DELETE,);
    }
}
