<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\User;
use App\Services\DisplayNotes;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/note', name: 'app_note')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'get_all_notes', methods: ['GET'])]
    public function getAllNotes(EntityManagerInterface $em, DisplayNotes $dn):JsonResponse
    {
        $notes = $em->getRepository(Note::class)->findAll();

        // Show error if the notes are not found
        if (!$notes) {
            return $this->json([
                'message' => 'There are no notes'
            ], 404);
        }

        // Custom method from services using getters
        $notesInfo = $dn->displayArray($notes);

        return $this->json([
            'message' => 'Notes retrieved',
            'data' => $notesInfo
        ]);
    }

    #[Route('/past', name: 'get_past_notes', methods: ['GET'])]
    public function getPastNotes(EntityManagerInterface $em, DisplayNotes $dn): JsonResponse
    {
        // Custom method in repository to fetch past notes
        $notes = $em->getRepository(Note::class)->getPastNotes();

        if (!$notes) {
            return $this->json([
                'message' => 'There are no notes'
            ], 404);
        }

        $notesInfo = $dn->displayArray($notes);

        return $this->json([
            'message' => 'Past notes retrieved',
            'data' => $notesInfo
        ]);
    }

    #[Route('/{id}', name: 'get_note_id', methods: ['GET'])]
    public function getNoteById($id, EntityManagerInterface $em, DisplayNotes $dn): JsonResponse
    {
        $note = $em->getRepository(Note::class)->find($id);

        if (!$note) {
            return $this->json([
                'message' => 'Note not found'
            ], 404);
        }

        // Custom method from services using getters
        $noteInfo = $dn->displayNote($note);

        return $this->json([
            'message' => 'Note retrieved',
            'data' => $noteInfo
        ]);
    }

    #[Route('/user/{id}', name: 'get_notes_by_user', methods: ['GET'])]
    public function getNotesByUser($id, EntityManagerInterface $em, DisplayNotes $dn): JsonResponse
    {
        $notes = $em->getRepository(User::class)->find($id)->getNotes();

        if (!$notes) {
            return $this->json([
                'message' => 'There are no notes'
            ], 404);
        }

        $notesInfo = $dn->displayArray($notes);

        return $this->json([
            'message' => 'Notes retrieved',
            'data' => $notesInfo
        ]);
    }

    #[Route('/cat/{id}', name: 'get_notes_by_category')]
    public function getNotesByCategory($id, EntityManagerInterface $em, DisplayNotes $dn): JsonResponse
    {
        $notes = $em->getRepository(Category::class)->find($id)->getNotes();
        $notesInfo = $dn->displayArray($notes);

        return $this->json([
            'message' => 'Notes retrieved',
            'data' => $notesInfo
        ]);
    }

    #[Route('/', name: 'create_note', methods: ['POST'])]
    public function createNote(EntityManagerInterface $em, Request $req): JsonResponse
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

    #[Route('/{id}', name: 'update_note', methods: ['PUT'])]
    public function editNote($id, EntityManagerInterface $em, Request $req, DisplayNotes $dn): JsonResponse
    {
        $note = $em->getRepository(Note::class)->find($id);

        $body = $req->toArray();

        if (!$note) {
            return $this->json([
                'message' => 'Note not found'
            ], 404);
        }

        // Check if the body is sending the fields to update the entity
        if (isset($body['title'])) {
            $note->setTitle($body['title']);
        }

        if (isset($body['description'])) {
            $note->setTitle($body['description']);
        }

        // Set new date to the note
        $note->setDate(new DateTime('now'));

        $em->persist($note);
        $em->flush();

        return $this->json([
            'message' => 'Note updated',
            'note' => $dn->displayNote($note)
        ]);
    }

    #[Route('/{id}', name: 'delete_note', methods: ['DELETE'])]
    public function deleteNote($id, EntityManagerInterface $em): JsonResponse
    {
        $note = $em->getRepository(Note::class)->find($id);

        if (!$note) {
            return $this->json([
                'message' => 'Note not found'
            ], 404);
        }

        $em->remove($note);
        $em->flush();

        return $this->json([
            'message' => 'Note deleted'
        ]);
    }
}
