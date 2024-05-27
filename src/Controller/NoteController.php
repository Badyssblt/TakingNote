<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Entity\UserGroups;
use App\Entity\UserPermission;
use App\Form\NoteCreateFormType;
use App\Form\NoteEditFormType;
use App\Repository\NoteRepository;
use App\Repository\UserGroupsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Serializer;

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

    #[Route('/getNotes', name: 'app_note_api')]
    public function notes(NoteRepository $noteRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_home');
        }

        $notes = $noteRepository->findBy(['author' => $user]);

        return $this->json(['notes' => $notes], Response::HTTP_OK, [], ["groups" => 'read:note:collection']);
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

        if ($request->isMethod('POST')) {
            if (!$this->user) {
                return $this->redirectToRoute('app_note');
            }

            $payload = $request->getPayload()->all();
            $name = $payload['note_name'];
            $content = $payload['note_content'];
            $category = $payload['note_category'];
            $note = new Note();
            $note->setName($name);
            $note->setContent($content);
            $note->setCategory($category);
            $note->setAuthor($this->user);
            $note->setIsPublic(false);
            $manager->persist($note);
            $manager->flush();
            return $this->json(['message' => 'Note créer']);
        }
        return $this->render('note/create/index.html.twig', [
            'form' => $form->createView(),
            'numbers' => $numbers
        ]);
    }

    #[Route('/note/{id}', name: 'app_read_note')]
    public function readNote(Note $note, NoteRepository $noteRepository, UserGroupsRepository $userGroupsRepository): Response
    {
        if (!$this->user) {
            return $this->redirectToRoute('app_home');
        }
        $numbers = $noteRepository->count(['author' => $this->user]);
        if ($note->getAuthor() === $this->getUser()) {
            $isAuthor = true;
        } else {
            $isAuthor = false;
        }

        $groups = $note->getUserGroups();
        $groupPermission = null;
        if ($groups !== null) {
            $groupPermission = $groups->getUserPermission();
        }
        return $this->render('note/read/index.html.twig', [
            'note' => $note,
            'numbers' => $numbers,
            'is_author' => $isAuthor,
            'group' => $groupPermission
        ]);
    }




    #[Route('/note/edit/{id}', name: 'app_edit_note')]
    public function editNote(UserGroupsRepository $userGroupsRepository, NoteRepository $noteRepository, Note $note, Request $request, EntityManagerInterface $manager): Response
    {
        $numbers = $noteRepository->count(['author' => $this->user]);
        $groups = $note->getUserGroups();
        $isWriter = false;

        // Vérifie si l'utilisateur connecté est dans le groupe de la note ou l'auteur
        $authorized = false;
        foreach ($groups->getUserPermission() as $user) {
            if ($user->getUser() === $this->user) {
                $authorized = true;
                break;
            }
        }

        if ($note->getAuthor() !== $this->user && !$authorized) {
            return $this->redirectToRoute("app_dashboard");
        } else {
            $isWriter = true;
        }

        foreach ($groups->getUserPermission() as $user) {
            foreach ($user->getRole() as $role) {
                if ($role == "WRITER") $isWriter = true;
            }
        }


        $form = $this->createForm(NoteEditFormType::class);

        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $payload = $request->getPayload()->all();
            $name = $payload['note_name'];
            $category = $payload['note_category'];
            $content = $payload['note_content'];
            $note->setName($name);
            $note->setContent($content);
            $note->setCategory($name);
            $manager->persist($note);
            $manager->flush();
            return $this->json(['message' => 'note modifié']);
        }

        return $this->render('note/edit/index.html.twig', [
            'form' => $form->createView(),
            'note' => $note,
            'numbers' => $numbers,
            'is_writer' => $isWriter
        ]);
    }


    #[Route('/note/delete/{id}', name: 'app_delete_note')]
    public function deleteNote(Note $note, Request $request, EntityManagerInterface $manager): Response
    {
        $manager->remove($note);
        $manager->flush();

        return $this->redirectToRoute('app_note');
    }

    #[Route("/notes/shared", methods: ['GET'], name: "app_notes_shared")]
    public function notesShared(NoteRepository $noteRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute("app_login");
        }

        $userPermissions = $user->getUserPermissions();
        $numbers = $noteRepository->count(['author' => $this->user]);


        $userGroups = [];

        foreach ($userPermissions as $userPermission) {
            $group = $userPermission->getUserGroups();

            if ($group && !in_array($group, $userGroups, true)) {
                $userGroups[] = $group;
            }
        }
        $notes = [];

        foreach ($userGroups as $group) {
            $groupNotes = $group->getNote();

            foreach ($groupNotes as $note) {
                $notes[] = $note;
            }
        }
        return $this->render('note/index.html.twig', [
            'notes' => $notes,
            'numbers' => $numbers
        ]);
    }


    #[Route('/notes/{sort}', name: 'app_note_sorted', defaults: ['sort' => 'date'])]
    public function sort(NoteRepository $noteRepository, string $sort): Response
    {

        if (!$this->user) {
            return $this->redirectToRoute('app_home');
        }

        $numbers = $noteRepository->count(['author' => $this->user]);

        if ($sort === 'name') {
            $notes = $noteRepository->findBy(['author' => $this->user], ['name' => 'ASC']);
        } else if ($sort === 'date') {
            $notes = $noteRepository->findBy(['author' => $this->user], ['created_at' => 'DESC']);
        }

        return $this->render('note/index.html.twig', [
            'notes' => $notes,
            'sort' => $sort,
            'numbers' => $numbers
        ]);
    }

    #[Route('/note/share/{id}')]
    public function shareNote(Note $note, EntityManagerInterface $manager, Request $request)
    {
        $modify = $request->query->get('modify');
        $isPublic = $request->query->get('visibility');
        if ($this->user != $note->getAuthor()) {
            return $this->redirectToRoute('app_dashboard');
        }

        if ($note->getUserGroups() == null) {
            $userGroup = new UserGroups();
            $userPerm = new UserPermission();
            $userPerm->setUserGroups($userGroup);
            $manager->persist($userPerm);
        }

        if ($note->isPublic() && $isPublic) {
            $note->setIsPublic(false);
        } else {
            $note->setIsPublic(true);
        }

        if ($note->getAuthor() === $this->getUser()) {
            $manager->persist($note);
            $manager->flush();
            return $this->redirectToRoute('app_read_note', ['id' => $note->getId()]);
        } else {
            return $this->render('home/index.html.twig');
        }
    }

    #[Route("/note/{id}/user/{userId}", methods: ["POST", "GET"])]
    public function addUserNote(UserRepository $userRepository, EntityManagerInterface $manager, Note $note, Request $request, int $userId)
    {
        if ($this->user != $note->getAuthor()) return $this->redirectToRoute("app_dashboard");

        $role = [$request->query->get("role")];
        $currentUser = $userRepository->find($userId);
        if ($note->getUserGroups() == null) {
            $userGroup = new UserGroups();
            $userPerm = new UserPermission();
            $userPerm->setUser($currentUser);
            $userPerm->setRole($role);
            $userPerm->setUserGroups($userGroup);
            $note->setUserGroups($userGroup);
            $manager->persist($userPerm);
            $manager->persist($note);
        } else {
            $userGroup = $note->getUserGroups();
            $userPerm = new UserPermission();
            $userPerm->setUser($currentUser);
            $userPerm->setRole($role);
            $userPerm->setUserGroups($userGroup);
            $manager->persist($userPerm);
        }

        $manager->flush();
        return $this->redirectToRoute('app_read_note', ['id' => $note->getId()]);
    }
}
