<?php
/**
 * Category Service Test.
 */

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CategoryServiceTest.
 * @property $entityManager
 */
class CategoryServiceTest extends WebTestCase
{
    /**
     * Category service.
     */
    private ?CategoryService $categoryService;

    /**
     * @return void void
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->categoryService = $container->get(CategoryService::class);
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Category::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    /**
     * Test GetPaginatedList.
     *
     * @return void void
     */
    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $dataSetSize = 5;
        $expectedResultSize = 5;
        $categoryRepository =
            static::getContainer()->get(CategoryRepository::class);

        $i = 0;
        while ($i < $dataSetSize) {
            $category = new Category();
            $category->setName('Categoryx'.$i);
            $categoryRepository->save($category);

            ++$i;
        }
        // when
        $result = $this->categoryService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Test delete.
     *
     * @throws ORMException
     */
    public function testDelete(): void
    {
        // given
        $page = 1;
        $categoryRepository =
            static::getContainer()->get(CategoryRepository::class);
        $categoryToDelete = new Category();
        $categoryToDelete->setName('Test Category');
        $deletedCategoryId = $categoryToDelete->getId();

        $categoryRepository->save($categoryToDelete);

        $before = $this->categoryService->getPaginatedList($page);

        // when
        $this->categoryService->delete($categoryToDelete);

        $after = $this->categoryService->getPaginatedList($page);
        // then
//        $resultCategory = $this->entityManager->createQueryBuilder()
//            ->select('category')
//            ->from(Category::class, 'category')
//            ->where('category.id = :id')
//            ->setParameter(':id', $deletedCategoryId, Types::INTEGER)
//            ->getQuery()
//            ->getOneOrNullResult();

        $this->assertEquals($before->count(), $after->count());
    }
}