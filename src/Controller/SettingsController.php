<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SettingsFormType;
use App\Form\SettingsPasswordFormType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class SettingsController extends AbstractController
{

    private User|null $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }


    #[Route('/user/settings', name: 'app_settings')]
    public function index(EntityManagerInterface $manager, NoteRepository $noteRepository, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        if (!$this->user) {
            return $this->redirectToRoute('app_login');
        }

        $numbers = $noteRepository->count(['author' => $this->user]);

        $personalForm = $this->createForm(SettingsFormType::class, $this->user, [
            'allow_file_upload' => false
        ]);

        $personalForm->handleRequest($request);

        if ($personalForm->isSubmitted() && $personalForm->isValid()) {
            $data = $personalForm->getData();
            $email = $data->getEmail();
            $name = $data->getName();
            if (!$email == "") {
                $this->user->setEmail($email);
            }

            if (!$name == "") {
                $this->user->setName($name);
            }

            $manager->persist($this->user);
            $manager->flush();

            return $this->redirectToRoute('app_dashboard');
        }


        $passwordForm = $this->createForm(SettingsPasswordFormType::class);

        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $payload = $request->getPayload()->all();
            $password = $payload['settings_password_form']['plainPassword'];

            $password = $hasher->hashPassword($this->user, $password);

            $this->user->setPassword($password);
            $manager->persist($this->user);
            $manager->flush();

            return $this->redirectToRoute('app_login');
        }


        return $this->render('settings/index.html.twig', [
            'numbers' => $numbers,
            'personalForm' => $personalForm->createView(),
            'passwordForm' => $passwordForm->createView()
        ]);
    }
}
