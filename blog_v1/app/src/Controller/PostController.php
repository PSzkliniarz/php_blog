<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\AddCommentType;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Monolog\DateTimeImmutable;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * Class PostController
 */
#[Route('/post')]
class PostController extends AbstractController
{
    /**
     * @param Request $request
     * @param PostRepository $postRepository
     * @param CategoryRepository $categoryRepository
     * @param PaginatorInterface $paginator
     * @return Response
     * Show post list
     */
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(Request $request, PostRepository $postRepository, CategoryRepository $categoryRepository,
                          PaginatorInterface $paginator): Response
    {
        $categoryId = $request->query->get('category');
        $filteredPost = $postRepository->findBy(['category'=> $categoryId]);
        if (count($filteredPost) > 0){
            $returnValue = $filteredPost;
        } else {
            $returnValue = $postRepository->findAll();
        }

        $pagination = $paginator->paginate(
            $returnValue,
            $request->query->getInt('page', 1),
            PostRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render('post/index.html.twig', [
            'posts' => $pagination,
            'categories' => $categoryRepository->findAll()
        ]);
    }

    /**
     * @param Request $request
     * @param PostRepository $postRepository New
     * @param Post $post
     * @return Response
     * @throws \Exception
     */
    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PostRepository $postRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $post = new Post();
        $post->setAuthor($user);
        $post->setCreatedAt(
            DateTimeImmutable::createFromMutable(
                new \DateTime('@'.strtotime('now'))
            )
        );
        $post->setUpdatedAt(
            DateTimeImmutable::createFromMutable(
                new \DateTime('@'.strtotime('now'))
            )
        );
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postRepository->add($post, true);

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/new.html.twig', [
            'post' => $post,
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
    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
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
    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', subject: 'post')]
    public function edit($id, Request $request, PostRepository $postRepository, EntityManagerInterface $em, Post $post): Response
    {
        $post = $postRepository->findOneBy(['id'=>$id]);
        $post->setUpdatedAt(
            DateTimeImmutable::createFromMutable(
                new \DateTime('@'.strtotime('now'))
            )
        );
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $postRepository->add($post, true);
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @param Request $request
     * @param Post $post
     * @param PostRepository $postRepository Delete post
     * @return Response
     */
    #[IsGranted('DELETE', subject: 'post')]
    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $postRepository->remove($post, true);
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
