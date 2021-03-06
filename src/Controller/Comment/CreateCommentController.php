<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Security\UserResolver;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/articles/{slug}/comments", methods={"POST"}, name="api_comment_post")
 *
 * @View(statusCode=201)
 *
 * @Security("is_granted('ROLE_USER')")
 */
final class CreateCommentController extends AbstractController
{
    private UserResolver $userResolver;
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserResolver $userResolver,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->userResolver = $userResolver;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, Article $article): array
    {
        $user = $this->userResolver->getCurrentUser();

        $comment = new Comment();
        $comment->setAuthor($user);
        $comment->setArticle($article);

        $form = $this->formFactory->createNamed('comment', CommentType::class, $comment);
        $form->submit($request->request->get('comment'));

        if ($form->isValid()) {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return ['comment' => $comment];
        }

        return ['form' => $form];
    }
}
