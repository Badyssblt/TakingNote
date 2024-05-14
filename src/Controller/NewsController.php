<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewsController extends AbstractController
{
    #[Route('/news', name: 'app_news')]
    public function index(NewsRepository $newsRepository): Response
    {
        $articles = $newsRepository->findBy(['category' => 'news', 'isPublic' => true]);
        $nextArticles = $newsRepository->findBy(['category' => 'next', 'isPublic' => true]);
        $currentTime = new \DateTime();
        return $this->render('news/index.html.twig', [
            'articles' => $articles,
            'nextArticles' => $nextArticles,
            'current' => $currentTime
        ]);
    }

    #[Route('/new/{id}', name: 'app_new')]
    public function new(News $article)
    {
        return $this->render('news/new/index.html.twig', [
            'article' => $article,
        ]);
    }
}
