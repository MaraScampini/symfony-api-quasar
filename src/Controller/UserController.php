<?php

namespace App\Controller;

use App\Entity\User;
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
    public function getAllUsers(EntityManagerInterface $em)
    {
        $data = $em->getRepository(User::class)->findAll();

        // Extract data for each user
        foreach ($data as $user) {
            $users[] = [
                'Name' => $user->getName(),
                'E-mail' => $user->getEmail(),
                'Age' => $user->getAge()
            ];
        }

        return $this->json([
            'message' => 'Users retrieved',
            'data' => $users
        ]);
    }

    #[Route('/{id}', name: 'get_user_by_id', methods: ['GET'])]
    public function getUserById(User $data)
    {
        // The search is already done using the ID parameter in the route, extract data from the user to show
        $user = [
            'Name' => $data->getName(),
            'E-mail' => $data->getEmail(),
            'Age' => $data->getAge()
        ];

        return $this->json([
            'message' => 'User retrieved',
            'data' => $user
        ]);
    }

    #[Route('/', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $req, EntityManagerInterface $em, ValidatorInterface $validator)
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
            
            foreach($errors as $error){
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
}
