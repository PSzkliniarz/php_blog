<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Monolog\DateTimeImmutable;
use Symfony\Component\Validator\Constraints\DateTime;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(Request $request, PostRepository $postRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator): Response
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


    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PostRepository $postRepository): Response
    {
        $post = new Post();
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

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Request $request , Post $post, CommentRepository $commentRepository): Response
    {
        $postId = $post->getId();
//        dump($postId);die;
//        $filteredPost = $postRepository->findBy(['category'=> $categoryId]);
        $filteredComment = $commentRepository->findBy(['post'=> $postId]);
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $filteredComment
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit($id, Request $request, PostRepository $postRepository, EntityManagerInterface $em): Response
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

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $postRepository->remove($post, true);
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
