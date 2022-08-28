<?php

namespace App\Test\Controller;

use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Tests\BaseTest;
use DateTime;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends BaseTest
{
    private CategoryRepository $repository;
    private string $path = '/category/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Category::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

//    public function testIndex(): void
//    {
//        $crawler = $this->client->request('GET', $this->path);
//
//        self::assertResponseStatusCodeSame(200);
//        self::assertPageTitleContains('Category index');
//
//        // Use the $crawler to perform additional assertions e.g.
//        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
//    }

    /**
     * Test index route for non-authorized user.
     *
     */
    public function testIndexRouteNormalUser(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value], 'category_user2@example.com');
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', $this->path);
        $resultStatusCode = $this->client->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultStatusCode);
    }

    /**
     * Test index route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testIndexRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'category_user@example.com');
        $this->client->loginUser($adminUser);

        // when
        $this->client->request('GET', $this->path);
        $resultStatusCode = $this->client->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

        public function testNew(): void
    {
        $user= $this->createUser([UserRole::ROLE_USER->value], 'category_new_user@example.com');
        $this->client->loginUser($user);

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));
        $result = $this->client->getResponse();

        self::assertResponseStatusCodeSame(200);
        $this->assertEquals(200, $result->getStatusCode());

        $this->client->submitForm('Save', [
            'category[name]' => 'Testing',
        ]);

        self::assertResponseRedirects('/category/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
        $this->assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    /**
     * Test show category
     */
    public function testShow(): void
    {
        $user= $this->createUser([UserRole::ROLE_USER->value], 'category_show_user@example.com');
        $this->client->loginUser($user);
        $fixture = new Category();
        $fixture->setName('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        $resultStatusCode = $this->client->getResponse()->getStatusCode();
        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Category');

        $this->assertEquals(200, $resultStatusCode);
    }

    public function testEdit(): void
    {
        $fixture = new Category();
        $fixture->setName('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $testCategoryId = $fixture->getId();
        $expectedNewCategoryName = 'TestCategoryEdit';

        $this->client->request('GET', $this->path . '/' .
            $testCategoryId . '/edit');

        // when
        $this->client->submitForm(
            'Edytuj',
            ['category' => ['name' => $expectedNewCategoryName]]
        );

        // then
        $savedCategory = $fixture->findOneById($testCategoryId);
        $this->assertEquals($expectedNewCategoryName,
            $savedCategory->getName());


    }


    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Category();
        $fixture->setName('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/category/');
    }
}
