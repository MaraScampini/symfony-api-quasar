<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'app_user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'get_users')]
    public function getAllUsers(EntityManagerInterface $em)
    {
        $data = $em->getRepository(User::class)->findAll();

        foreach($data as $user){
            $users[]=[
                'Name'=>$user->getName(),
                'E-mail'=>$user->getEmail(),
                'Age'=>$user->getAge()
            ];
        }
        
        return $this->json([
            'message' => 'Users retrieved',
            'data'=> $users
        ]);
    }
}
