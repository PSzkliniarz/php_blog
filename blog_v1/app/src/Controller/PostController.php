<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\AddCommentType;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Service\PostService;
use App\Service\PostServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Monolog\DateTimeImmutable;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Class PostController
 */
#[Route('/post')]
class PostController extends AbstractController
{
    /**
     * Post service.
     */
    private PostService $postService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     */
    public function __construct(PostServiceInterface $postService, TranslatorInterface $translator)
    {
        $this->postService = $postService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'post_index',
        methods: 'GET'
    )]
    public function index(Request $request): Response
    {
        $pagination = $this->postService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render(
            'post/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * @param Request $request
     * @param PostRepository $postRepository New
     * @param Post $post
     * @return Response
     * @throws \Exception
     */
    #[Route('/new', name: 'post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PostRepository $postRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $post = new Post();
        $post->setAuthor($user);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->postService->save($post);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('post_index');
        }

        return $this->renderForm('post/new.html.twig', [
            'form' => $form,
        ]);
    }



    /**
     * @param Request $request
     * @param Post $post
     * @param CommentRepository $commentRepository
     * @param EntityManagerInterface $em
     * @return Response
     * Show post
     */
    #[Route('/{id}', name: 'post_show', methods: ['GET'])]
    public function show(Request $request , Post $post, CommentRepository $commentRepository, EntityManagerInterface $em): Response
    {
        $postId = $post->getId();
        $comment = new Comment();
        $form = $this->createForm(AddCommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $comment->setPost($post);
            $em->persist($comment);
            $em->flush();
            $redirectUrl = $this->generateUrl('app_post_show', ['id'=>$postId]);
            return $this->redirect($redirectUrl);
        }
        $filteredComment = $commentRepository->findBy(['post'=> $postId]);
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $filteredComment,
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit action.
     *
     * @param Request     $request     HTTP request
     * @param Post $post Post entity
     *
     * @return Response HTTP response
     * Edit post
     */
    #[Route('/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', subject: 'post')]
    public function edit($id, Request $request, PostRepository $postRepository, EntityManagerInterface $em, Post $post): Response
    {
        $post = $postRepository->findOneBy(['id'=>$id]);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->postService->save($post);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('post_index');
        }

        return $this->renderForm('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Post    $post    Post entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete',
        name: 'post_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'post')]
    public function delete(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('post_delete', ['id' => $post->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->postService->delete($post);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('post_index');
        }

        return $this->render(
            'post/delete.html.twig',
            [
                'form' => $form->createView(),
                'post' => $post,
            ]
        );
    }
}
