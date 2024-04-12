<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Form\NoteCreateFormType;
use App\Form\NoteEditFormType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NoteController extends AbstractController
{

    private User|null $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/notes', name: 'app_note')]
    public function index(NoteRepository $noteRepository): Response
    {
        $numbers = $noteRepository->count(['author' => $this->user]);

        if (!$this->user) {
            return $this->redirectToRoute('app_home');
        }
        $notes = $noteRepository->findBy(['author' => $this->user]);
        return $this->render('note/index.html.twig', [
            'notes' => $notes,
            'numbers' => $numbers
        ]);
    }

    #[Route('/note/create', name: 'app_create_note')]
    public function createNote(Request $request, EntityManagerInterface $manager, NoteRepository $noteRepository): Response
    {
        if (!$this->user) {
            return $this->redirectToRoute('app_home');
        }

        $numbers = $noteRepository->count(['author' => $this->user]);

        $form = $this->createForm(NoteCreateFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->user) {
                return $this->redirectToRoute('app_note');
            }
            $payload = $request->getPayload()->all();
            $name = $payload['note_create_form']['note_name'];
            $content = $payload['note_create_form']['note_content'];
            $category = $payload['note_create_form']['note_category'];
            $note = new Note();
            $note->setName($name);
            $note->setContent($content);
            $note->setCategory($category);
            $note->setAuthor($this->user);
            $manager->persist($note);
            $manager->flush();
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('note/create/index.html.twig', [
            'form' => $form->createView(),
            'numbers' => $numbers
        ]);
    }

    #[Route('/note/{id}', name: 'app_read_note')]
    public function readNote(Note $note, NoteRepository $noteRepository): Response
    {
        if (!$this->user) {
            return $this->redirectToRoute('app_home');
        }
        $numbers = $noteRepository->count(['author' => $this->user]);

        return $this->render('note/read/index.html.twig', [
            'note' => $note,
            'numbers' => $numbers
        ]);
    }




    #[Route('/note/edit/{id}', name: 'app_edit_note')]
    public function editNote(Note $note, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(NoteEditFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payload = $request->getPayload()->all();
            $name = $payload['note_edit_form']['note_name'];
            $category = $payload['note_edit_form']['note_category'];
            $content = $payload['note_edit_form']['note_content'];
            $note->setName($name);
            $note->setContent($content);
            $note->setCategory($name);
            $manager->persist($note);
            $manager->flush();
            return $this->redirectToRoute('app_note');
        }

        return $this->render('note/edit/index.html.twig', [
            'form' => $form->createView(),
            'note' => $note
        ]);
    }


    #[Route('/note/delete/{id}', name: 'app_delete_note')]
    public function deleteNote(Note $note, Request $request, EntityManagerInterface $manager): Response
    {
        $manager->remove($note);
        $manager->flush();

        return $this->redirectToRoute('app_note');
    }
}
