<?php

namespace App\Controller;

use App\Entity\Friends;
use App\Repository\FriendsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FriendsController extends AbstractController
{
    #[Route("/friends", methods: ['POST'], name: "app_create_friends")]
    public function requestFriend(EntityManagerInterface $manager, FriendsRepository $friendsRepository, Request $request, UserRepository $userRepository)
    {
        $requestID = $request->getPayload()->get("requestUser");
        $requestUser = $userRepository->find($requestID);
        if ($friendsRepository->findOneBy(["user1" => $this->getUser(), "user2" => $requestUser]) == null && $friendsRepository->findOneBy(["user1" => $requestUser, "user2" => $this->getUser()]) == null) {
            $newFriend = new Friends();
            $newFriend->setUser1($this->getUser());
            $newFriend->setUser2($requestUser);
            $newFriend->setSender($this->getUser());
            $newFriend->setStatus("pending");
            $manager->persist($newFriend);
            $manager->flush();
            return $this->json($newFriend, Response::HTTP_OK, [], ["groups" => "post:item:friend"]);
        } else {
            return $this->json(["message" => "Cet utilisateur est déjà ajouté en amis"], Response::HTTP_CONFLICT, []);
        }
    }

    #[Route("/friends/{id}/denied", methods: ['GET', 'DELETE'])]
    public function deleteFriend(UserRepository $userRepository, int $id, EntityManagerInterface $manager, FriendsRepository $friendsRepository)
    {
        $requestUser = $userRepository->find($id);
        if ($friendsRepository->findOneBy(["user1" => $this->getUser(), "user2" => $requestUser]) == null && $friendsRepository->findOneBy(["user1" => $requestUser, "user2" => $this->getUser()]) != null) {
            $friend = $friendsRepository->findOneBy(["user1" => $requestUser, "user2" => $this->getUser()]);
            $manager->remove($friend);
        } else if ($friendsRepository->findOneBy(["user1" => $this->getUser(), "user2" => $requestUser]) != null && $friendsRepository->findOneBy(["user1" => $requestUser, "user2" => $this->getUser()]) == null) {
            $friend = $friendsRepository->findOneBy(["user1" => $this->getUser(), "user2" => $requestUser]);
            $manager->remove($friend);
        } else {
            return null;
        }

        $manager->flush();
        return $this->redirectToRoute("app_dashboard");
    }

    #[Route('/friends/{id}/accept', methods: ['GET', 'PATCH'])]
    public function acceptFriend(UserRepository $userRepository, int $id, EntityManagerInterface $manager, FriendsRepository $friendsRepository)
    {
        $requestUser = $userRepository->find($id);
        if ($friendsRepository->findOneBy(["user1" => $this->getUser(), "user2" => $requestUser]) == null && $friendsRepository->findOneBy(["user1" => $requestUser, "user2" => $this->getUser()]) != null) {
            $friend = $friendsRepository->findOneBy(["user1" => $requestUser, "user2" => $this->getUser()]);
            $friend->setStatus("accepted");
            $manager->persist($friend);
        } else if ($friendsRepository->findOneBy(["user1" => $this->getUser(), "user2" => $requestUser]) != null && $friendsRepository->findOneBy(["user1" => $requestUser, "user2" => $this->getUser()]) == null) {
            $friend = $friendsRepository->findOneBy(["user1" => $this->getUser(), "user2" => $requestUser]);
            $friend->setStatus("accepted");
            $manager->persist($friend);
        }

        $manager->flush();
        return $this->redirectToRoute("app_dashboard");
    }

    #[Route("/search/user")]
    public function searchFriends(Request $request, UserRepository $userRepository)
    {
        $name = $request->query->get('name');
        if (!$name) {
            return $this->json(["Veuillez entrer un nom"], Response::HTTP_BAD_REQUEST);
        }

        $results = $userRepository->findBySearch($name);
        return $this->json($results, Response::HTTP_OK, [], ['groups' => 'read:user:collection']);
    }
}
