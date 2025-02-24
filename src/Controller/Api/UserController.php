<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Resource\UserResource;
use App\Security\Voter\UserVoter;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/v1/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    public function __construct(
        protected SerializerInterface    $serializer,
        protected ValidatorInterface     $validator,
        protected UserService            $service,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getUsers(): JsonResponse
    {
        $resources = $this->service->list();

        return new JsonResponse($resources);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[IsGranted(UserVoter::VIEW, subject: 'user')]
    public function show(User $user): JsonResponse
    {
        $resource = UserResource::fromEntity($user)->getShowResource();

        return new JsonResponse($resource);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(UserVoter::CREATE, new User());
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $this->validate($user, ['create']);
        $resource = $this->service->create($user);

        return new JsonResponse($resource, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[IsGranted(UserVoter::EDIT, subject: 'user')]
    public function update(User $user, Request $request): JsonResponse
    {
        $updatedUser = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $this->validate($updatedUser, ['update']);
        $resource = $this->service->update($user, $updatedUser);

        return new JsonResponse($resource);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user): JsonResponse
    {
        $this->service->delete($user);

        return new JsonResponse(['success' => true]);
    }

    private function validate(User $user, array $groups): void
    {
        $errors = $this->validator->validate($user, null, $groups);

        if (count($errors) > 0) {
            throw new ValidationFailedException($user, $errors);
        }
    }
}
