<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/note', name: 'app_note')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'get_all_notes', methods: ['GET'])]
    public function getAllNotes(EntityManagerInterface $em)
    {
        $notes = $em->getRepository(Note::class)->findAll();

        foreach ($notes as $n) {
            $fullNotes[] = [
                'title' => $n->getTitle(),
                'description' => $n->getDescription(),
                'date' => $n->getDate()
            ];
        }

        return $this->json([
            'message' => 'Notes retrieved',
            'data' => $fullNotes
        ]);
    }

    #[Route('/', name: 'create_note', methods: ['POST'])]
    public function createNote(EntityManagerInterface $em, Request $req)
    {
        $body = $req->toArray();

        // Find the user who is going to be owner of the note
        $user = $em->getRepository(User::class)->find($body['user']);

        // Find the categories
        $categories = $body['categories'];

        foreach ($categories as $cat) {
            $newCategories[] = $em->getRepository(Category::class)->find($cat);
            
        }

        // Create the new note and assign all the properties
        $newNote = new Note();
        $newNote->setTitle($body['title']);
        $newNote->setDescription($body['description']);
        $newNote->setDate(new DateTime('now'));
        $newNote->setUser($user);

        foreach ($newCategories as $nc) {
            $newNote->addCategory($nc);
        }

        // Save to the DB
        $em->persist($newNote);
        $em->flush();

        return $this->json([
            'message' => 'Note created'
        ]);
    }
}
