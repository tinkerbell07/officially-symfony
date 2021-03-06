<?php

declare(strict_types=1);

namespace App\Controller\Article;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/articles/{slug}", methods={"DELETE"}, name="api_articles_delete")
 *
 * @Security("is_granted('ROLE_USER') and is_granted('AUTHOR', article)")
 */
final class DeleteArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Article $article): void
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }
}
