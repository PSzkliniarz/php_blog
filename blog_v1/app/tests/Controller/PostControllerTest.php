<?php

namespace App\Test\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Tests\BaseTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class PostControllerTest extends BaseTest
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/post';

    private PostRepository $repository;

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Post::class);
    }

    /**
     * Test index route
     *
     * @return void
     */
    public function testIndex(): void
    {
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'post_index_admin@example.com');
        $this->httpClient->loginUser($adminUser);
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $result = $this->httpClient->getResponse();

        $this->assertEquals($expectedStatusCode, $result->getStatusCode());
    }

    public function testNew(): void
    {
//        $originalNumObjectsInRepository = count($this->repository->findAll());
//
//        $this->markTestIncomplete();

        $user= $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'post_new_admin@example.com');
        $this->httpClient->loginUser($user);

        $category = $this->createCategory('Category_One');

        $this->httpClient->request('GET', self::TEST_ROUTE . '/new');;

        $this->httpClient->submitForm('Save', [
            'post[title]' => 'Testing',
            'post[content]' => 'Testing',
            'post[category]' => $category->getId(),
        ]);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals(302, $resultStatusCode);
    }

    public function testShow(): void
    {
        $user= $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'post_admin@example.com');
        $category = $this->createCategory();
        $fixture = $this->createPost($user, $category);

        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $fixture->getId());

        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals(200, $resultStatusCode);
        $this->assertEquals('post_admin@example.com', $fixture->getAuthor()->getEmail());
    }

    public function testEdit(): void
    {
        $postRepository =
            static::getContainer()->get(PostRepository::class);

        $user= $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'post_admin@example.com');
        $category = $this->createCategory();
        $fixture = $this->createPost($user, $category);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'. $fixture->getId().'/edit');;

        $newTitle = 'New Title';
        $newContent = 'New Content';
        $this->httpClient->submitForm('Update', [
            'post[title]' => $newTitle,
            'post[content]' => $newContent,
        ]);
//        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
//
//        $this->assertEquals(200, $resultStatusCode);
        $savedQuestion = $postRepository->findOneById($fixture->getId());
        $this->assertEquals(
            $newTitle,
            $savedQuestion->getTitle()
        );
    }

    public function testRemove(): void
    {

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $user= $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'post_admin@example.com');
        $category = $this->createCategory();
        $fixture = $this->createPost($user, $category);

//        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'. $fixture->getId().'/delete');;
        $this->httpClient->submitForm('Delete');

        $this->assertEquals(200, 2);
    }
}
