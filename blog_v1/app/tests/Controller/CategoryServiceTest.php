<?php
/**
 * Category Service Test.
 */

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
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
     * Test entity manager.
     *
     * @var EntityManagerInterface|object|null
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * @return void void
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->categoryService = $container->get(CategoryService::class);
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Category::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    /**
     * Delete test.
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testDelete(): void
    {
        // given
        $categoryToDelete = new Category();
        $categoryToDelete->setCreatedAt(new \DateTimeImmutable());
        $categoryToDelete->setUpdatedAt(new \DateTimeImmutable());
        $categoryToDelete->setName('Test Category');
        $this->entityManager->persist($categoryToDelete);
        $this->entityManager->flush();
        $deletedCategoryId = $categoryToDelete->getId();

        // when
        $this->categoryService->delete($categoryToDelete);

        // then
        $resultCategory = $this->entityManager->createQueryBuilder()
            ->select('category')
            ->from(Category::class, 'category')
            ->where('category.id = :id')
            ->setParameter('id', $deletedCategoryId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultCategory);
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
}
