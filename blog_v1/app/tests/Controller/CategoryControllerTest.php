<?php

namespace App\Test\Controller;

use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Tests\BaseTest;
use DateTimeImmutable;
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
//    private CategoryRepository $repository;
//
//    /**
//     * Test route.
//     *
//     * @const string
//     */
//    public const TEST_ROUTE = '/category';
//
//    protected function setUp(): void
//    {
//        $this->client = static::createClient();
//        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Category::class);
//
//        foreach ($this->repository->findAll() as $object) {
//            $this->repository->remove($object, true);
//        }
//    }

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
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/category';


    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Category::class);
    }

    /**
     * @return void
     */
    public function testIndexRouteAnonymousUser(): void
    {
        // given
        $user = null;
        $expectedStatusCode = 200;
        try {
            $user = $this->createUser([UserRole::ROLE_ADMIN->value], 'category_user@example.com');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface|ORMException $e) {
        }
        $this->logIn($user);
        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

//    /**
//     * @return void
//     */
//    public function testIndexRouteAnonymousUser(): void
//    {
//        // given
//        $user = null;
//        $expectedStatusCode = 200;
//        try {
//            $user = $this->createUser([UserRole::ROLE_ADMIN->value], 'categoryindexuser@example.com');
//        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface|ORMException $e) {
//        }
//        $this->logIn($user);
//        // when
//        $this->httpClient->request('GET', self::TEST_ROUTE);
//        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
//
//        // then
//        $this->assertEquals($expectedStatusCode, $resultStatusCode);
//    }


    /**
     * Test index route for non-authorized user.
     *
     */
    public function testIndexRouteNormalUser(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value], 'category_user1@example.com');
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

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
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'category_user4@example.com');
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

        public function testNew(): void
    {
        $user= $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'category_new_admin@example.com');
        $this->httpClient->loginUser($user);

        $originalNumObjectsInRepository = count($this->repository->findAll());

//        $this->httpClient->request('GET', sprintf('%snew', self::TEST_ROUTE));
        $this->httpClient->request('GET', self::TEST_ROUTE . '/create');
        $result = $this->httpClient->getResponse();

//        self::assertResponseStatusCodeSame(200);
        $this->assertEquals(200, $result->getStatusCode());

        $this->httpClient->submitForm('Save', [
            'category[name]' => 'Testing',
        ]);

//        self::assertResponseRedirects('/category/');

//        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
        $this->assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    /**
     * Test show category
     */
    public function testShow(): void
    {
        $user= $this->createUser([UserRole::ROLE_USER->value], 'category_show_user@example.com');
        $this->httpClient->loginUser($user);
        $fixture = new Category();
        $fixture->setName('My Title');

        $this->repository->save($fixture);

        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $fixture->getId());

        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals(200, $resultStatusCode);
    }

    public function testEdit(): void
    {
//        $fixture = new Category();
//        $fixture->setName('My Title');
//
//        $this->repository->add($fixture, true);
        $user= $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'category_edit_user@example.com');
        $this->httpClient->loginUser($user);
        $fixture = new Category();
        $fixture->setName('My Title');

        $this->repository->save($fixture);

        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $fixture->getId());

        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

//        self::assertResponseStatusCodeSame(200);
//        $this->assertEquals(200, $result->getStatusCode());

        $this->httpClient->submitForm('Save', [
            'category[name]' => 'Testing',
        ]);
        $expectedNewCategoryName = 'TestCategoryEdit';
        $this->httpClient->request('GET', self::TEST_ROUTE.'/'. $fixture->getId().'/edit');



        // when
        $this->httpClient->submitForm(
            'Edytuj',
            ['category' => ['name' => $expectedNewCategoryName]]
        );

        // then
//        $savedCategory = $fixture->findOneById($testCategoryId);
//        $this->assertEquals($expectedNewCategoryName,
//            $savedCategory->getName());
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals(200, $resultStatusCode);


    }


    public function testRemove(): void
    {
        $user= $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'category_remove_admin@example.com');
        $this->httpClient->loginUser($user);

//        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Category();
        $fixture->setName('My Title To Delete');
        $fixture->setCreatedAt(new \DateTimeImmutable('now'));
        $fixture->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->repository->save($fixture);

//        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

//        $this->client->request('GET', sprintf('%s%s', self::TEST_ROUTE, $fixture->getId()));
        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $fixture->getId() . '/delete');
        $this->httpClient->submitForm('Delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

//        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
//        self::assertResponseRedirects('/category/');
//        $this->assertNull($fixture->findOneByName('My Title To Delete'));
//        $this->assertSame(1, count($this->repository->findAll()));
//        $this->assertEquals(200, $resultStatusCode);
        $this->assertNull($fixture->findOneByName('My Title To Delete'));
    }
}
