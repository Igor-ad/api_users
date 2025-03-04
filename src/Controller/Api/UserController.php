<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Enum\Roles;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/v1/api/users', name: 'api_users_')]
class UserController extends BaseController
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected ValidatorInterface  $validator,
        protected UserService         $service,
        protected UserRepository      $repository,
    ) {
        parent::__construct($this->serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted(Roles::Admin->value)]
    public function getUsers(): JsonResponse
    {
        $resource = ['users' => $this->repository->findAll()];

        return $this->jsonResponse(
            $resource,
            'All users.',
            Response::HTTP_OK,
            ['public', 'detail']
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[IsGranted(UserVoter::VIEW, subject: 'user')]
    public function show(User $user): JsonResponse
    {
        $group = (getenv('APP_ENV') === 'test') ? ['private', 'public'] : ['public'];

        return $this->jsonResponse(
            ['users' => [$user]],
            'View user information.',
            Response::HTTP_OK,
            $group,
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(UserVoter::CREATE, new User());
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            ['allow_extra_attributes' => false],
        );
        $this->validate($user, ['create']);
        $resource = $this->service->create($user);
        $group = (getenv('APP_ENV') === 'test') ? ['private', 'public', 'detail'] : ['public', 'detail'];

        return $this->jsonResponse(
            $resource,
            'New user created.',
            Response::HTTP_CREATED,
            $group,
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[IsGranted(UserVoter::EDIT, subject: 'user')]
    public function update(User $user, Request $request): JsonResponse
    {
        $updatedUser = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            ['allow_extra_attributes' => false],
        );
        $this->validate($updatedUser, ['update']);
        $resource = $this->service->update($user, $updatedUser);

        return $this->jsonResponse(
            $resource,
            'User information has been updated.',
            Response::HTTP_OK,
            ['detail'],
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted(Roles::Admin->value)]
    public function delete(User $user): JsonResponse
    {
        $this->repository->remove($user, true);

        return $this->jsonResponse(
            ['success' => true],
            'The user has been removed from the system.',
        );
    }

    private function validate(User $user, array $groups): void
    {
        $errors = $this->validator->validate($user, null, $groups);

        if (count($errors) > 0) {
            throw new ValidationFailedException($user, $errors);
        }
    }
}
