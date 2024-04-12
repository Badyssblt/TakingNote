<?php

namespace App\Controller;

use App\Entity\BlocNote;
use App\Entity\User;
use App\Form\BlocNoteCreateFormType;
use App\Repository\BlocNoteRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{

    private User|null $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(EntityManagerInterface $manager, NoteRepository $noteRepository, Security $security, Request $request): Response
    {
        if (!$this->user) {
            return $this->redirectToRoute('app_home');
        }
        $numbers = $noteRepository->count(['author' => $this->user]);
        $notes = $noteRepository->findByLimit(5, $this->user);
        $blocNoteForm = $this->createForm(BlocNoteCreateFormType::class);

        $blocNoteForm->handleRequest($request);

        if ($blocNoteForm->isSubmitted() && $blocNoteForm->isValid()) {
            $payload = $request->getPayload()->all();
            $name = $payload['bloc_note_create_form']['bloc_name'];
            $blocNote = new BlocNote();
            $blocNote->setName($name);
            $blocNote->setAuthor($this->user);
            $manager->persist($blocNote);
            $manager->flush();
            return $this->redirectToRoute('app_dashboard');
        }

        $blocNotes = $this->user->getBlocNotes();


        return $this->render('dashboard/index.html.twig', [
            'notes' => $notes,
            'numbers' => $numbers,
            'blocNoteForm' => $blocNoteForm,
            'blocnotes' => $blocNotes
        ]);
    }
}
