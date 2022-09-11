<?php
/**
 * Post service.
 */

namespace App\Service;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PostService.
 */
class PostService implements PostServiceInterface
{
    /**
     * Post repository.
     */
    private PostRepository $postRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * CategoryRepository.
     */
    private CategoryRepository $categoryRepository;


    /**
     * Category Service.
     */
    private CategoryService $categoryService;

    /**
     * Constructor.
     *
     * @param PostRepository     $postRepository Post repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(PostRepository $postRepository, PaginatorInterface $paginator, CategoryRepository $categoryRepository, CategoryService $categoryService)
    {
        $this->postRepository = $postRepository;
        $this->paginator = $paginator;
        $this->categoryRepository = $categoryRepository;
        $this->categoryService = $categoryService;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     * @param array $filters Filters
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, array $filters = [] ): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->postRepository->queryAll($filters),
            $page,
            PostRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Post $post Post entity
     */
    public function save(Post $post): void
    {
        if (null === $post->getId()) {
            $post->setCreatedAt(new \DateTimeImmutable());
        }
        $post->setUpdatedAt(new \DateTimeImmutable());

        $this->postRepository->save($post);
    }

    /**
     * Delete entity.
     *
     * @param Post $post Post entity
     */
    public function delete(Post $post): void
    {
        $this->postRepository->delete($post);
    }

    /**
     * Prepare filters function.
     *
     * @param array $filters Filters
     *
     */
    public function prepareFilters(array $filters): array
    {
        $resultFilters = [];


        if (!empty($filters['category_id'])) {
            $category = $this->categoryService->findOneById($filters['category_id']);
            if (null !== $category) {
                $resultFilters['category'] = $category;
            }
        }

        return $resultFilters;
    }

}
