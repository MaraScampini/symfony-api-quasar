<?php

namespace App\Controller;

use App\Entity\Category;
use App\Services\DisplayCategories;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: 'app_category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'create_category', methods: ['POST'])]
    public function createCategory(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $body = $req->toArray();

        $newCat = new Category();
        $newCat->setName($body['name']);

        $em->persist($newCat);
        $em->flush();

        return $this->json([
            'message' => 'Category created',
            'data' => $newCat->getName()
        ]);
    }

    #[Route('/', name: 'get_categories', methods: ['GET'])]
    public function getCategories(EntityManagerInterface $em, DisplayCategories $dc): JsonResponse
    {
        $data = $em->getRepository(Category::class)->findAll();
        $categories = $dc->displayArray($data);

        return $this->json([
            'message' => 'These are the available categories',
            'data' => $categories
        ]);
    }

    #[Route('/{id}', name: 'edit_category', methods: ['PUT'])]
    public function editCategory(EntityManagerInterface $em, Request $req, $id): JsonResponse
    {
        $body = $req->toArray();

        $category = $em->getRepository(Category::class)->find($id);

        if (!$category) {
            return $this->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->setName($body['name']);
        $em->persist($category);
        $em->flush();

        return $this->json([
            'message' => 'Category updated',
            'data' => $category->getName()
        ]);
    }

    #[Route('/{id}', name: 'delete_category', methods: ['DELETE'])]
    public function deleteCategory(EntityManagerInterface $em, $id): JsonResponse
    {
        $category = $em->getRepository(Category::class)->find($id);

        if (!$category) {
            return $this->json([
                'message' => 'Category not found'
            ], 404);
        }

        $em->remove($category);
        $em->flush();

        return $this->json([
            'message' => 'Category deleted'
        ]);
    }
}
