<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\DisplayUsers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/user', name: 'app_user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'get_users', methods: ['GET'])]
    public function getAllUsers(EntityManagerInterface $em, DisplayUsers $du): JsonResponse
    {
        $data = $em->getRepository(User::class)->findAll();

        // Custom method from services using getters
        $users = $du->displayArrayUsers($data);

        return $this->json([
            'message' => 'Users retrieved',
            'data' => $users
        ]);
    }

    #[Route('/{id}', name: 'get_user_by_id', methods: ['GET'])]
    public function getUserById(EntityManagerInterface $em, DisplayUsers $du, $id): JsonResponse
    {
        $data = $em->getRepository(User::class)->find($id);
        $user = $du->displayUser($data);

        // If no user is found, communicate it
        if (!$data) {
            return $this->json([
                'message' => 'User not found'
            ], 404);
        }

        return $this->json([
            'message' => 'User retrieved',
            'data' => $user
        ]);
    }

    #[Route('/', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $req, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        // Parse the body to array to access the data
        $body = $req->toArray();

        // Create a new instance of the User class
        $newUser = new User();
        $newUser->setName($body['name']);
        $newUser->setAge($body['age']);
        $newUser->setEmail($body['email']);

        $em->persist($newUser);

        // Use validator to check the data
        $errors = $validator->validate($newUser);

        // If errors are encountered, show them to the user
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorMessage[] = $error->getMessage();
            }
            return $this->json([
                'message' => 'Error creating the user',
                'error' => $errorMessage
            ]);
        }

        // If no errors are encountered, save the data to the DB
        $em->flush();

        return $this->json([
            'message' => 'User created'
        ]);
    }

    #[Route('/', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $body = $req->toArray();

        $user_id = $body['id'];

        // Find the user
        $user = $em->getRepository(User::class)->find($user_id);

        // If the user does not exist, throw error
        if (!$user) {
            return $this->json([
                'message' => 'User not found'
            ], 404);
        }

        // Check which fields are filled to update the user. Email cannot be changed.
        if (isset($body['name'])) {
            $name = $body['name'];
            $user->setName($name);
        }
        if (isset($body['age'])) {
            $age = $body['age'];
            $user->setAge($age);
        }

        // Save to the DB
        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'User updated'
        ], 200);
    }

    #[Route('/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(EntityManagerInterface $em, $id): JsonResponse
    {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json([
                'message' => 'User not found'
            ], 404);
        }

        $em->remove($user);
        $em->flush();

        return $this->json([
            'message' => 'User deleted'
        ], 200);
    }
}
