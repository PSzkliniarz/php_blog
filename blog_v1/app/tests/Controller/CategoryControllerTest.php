<?php

namespace App\Test\Controller;

use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Tests\BaseTest;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;


class CategoryControllerTest extends BaseTest
{
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
     * Test index route for Normal user.
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
     */
    public function testIndexRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'category_admin@example.com');
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test New category
     */
    public function testNew(): void
    {
        $user= $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'category_new_admin@example.com');
        $this->httpClient->loginUser($user);

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->httpClient->request('GET', self::TEST_ROUTE . '/create');
        $result = $this->httpClient->getResponse();

        $this->assertEquals(200, $result->getStatusCode());

        $this->httpClient->submitForm('Save', [
            'category[name]' => 'Testing',
        ]);

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
}
