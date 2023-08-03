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
        $noteRepo = $em->getRepository(Note::class);
        $noteData = $noteRepo->findAll();

        $notes = $noteRepo->displayNotes($noteData);

        return $this->json([
            'message' => 'Notes retrieved',
            'data' => $notes
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

    #[Route('/past', name: 'get_past_notes', methods: ['GET'])]
    public function getPastNotes(EntityManagerInterface $em)
    {
        $noteRepo = $em->getRepository(Note::class);
        $pastNotes=$noteRepo->getPastNotes();

        $notes = $noteRepo->displayNotes($pastNotes);

        return $this->json([
            'message'=> 'Past notes retrieved',
            'data' => $notes
        ]);
    }

    #[Route('/{id}', name: 'get_note_id', methods: ['GET'])]
    public function getNoteById($id, EntityManagerInterface $em)
    {
        $noteRepo = $em->getRepository(Note::class);
        $note = $noteRepo->find($id);
        $noteInfo = $noteRepo->displayNote($note);

        return $this->json([
            'message' => 'Note retrieved',
            'data' => $noteInfo
        ]);
    }

    #[Route('/user/{id}', name: 'get_notes_by_user', methods: ['GET'])]
    public function getNotesByUser($id, EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find($id);
        $noteRepo = $em->getRepository(Note::class);

        $notes = $user->getNotes();
        $notesInfo = $noteRepo->displayNotes($notes);
        
        return $this->json([
            'message' => 'Notes retrieved for user ' . $user->getName(),
            'data' => $notesInfo
        ]);
    }
}
