<?php

namespace App\Test\Controller;

use App\Entity\Comment;
use App\Entity\Enum\UserRole;
use App\Repository\CommentRepository;
use App\Tests\BaseTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentControllerTest extends BaseTest
{
    private CommentRepository $repository;
    private string $path = '/comment/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Comment::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    /**
     * Test Index
     */
    public function testIndex(): void
    {
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'comment_admin1@example.com');
        $this->client->loginUser($adminUser);
        $crawler = $this->client->request('GET', $this->path);
        $result = $this->client->getResponse();

        $this->assertEquals($expectedStatusCode, $result->getStatusCode());
    }

    /**
     * Test New
     */
    public function testNew(): void
    {
        $expectedStatusCode = 200;
        $userEmail = 'comment_new_user@example.com';
        $adminUser = $this->createUser([UserRole::ROLE_USER->value], $userEmail);
        $this->client->loginUser($adminUser);
        $category = $this->createCategory();
        $post = $this->createPost($adminUser, $category);

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));
        $result = $this->client->getResponse();


        $this->client->submitForm('Save', [
            'comment[commentText]' => 'Testing',
            'comment[autor]' => $userEmail,
            'comment[post]' => $post->getId(),
        ]);

        $this->assertEquals($expectedStatusCode, $result->getStatusCode());
        $this->assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    /**
     * Test Show
     */
    public function testShow(): void
    {
        $expectedStatusCode = 200;
        $userEmail = 'comment_show_user@example.com';
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'comment_show_user@example.com');
        $this->client->loginUser($adminUser);
        $category = $this->createCategory();
        $post = $this->createPost($adminUser, $category);
        $fixture = new Comment();
        $fixture->setCommentText('My Title');
        $fixture->setAutor($userEmail);
        $fixture->setPost($post);

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $result = $this->client->getResponse();

        $this->assertEquals($expectedStatusCode, $result->getStatusCode());

        // Use assertions to check that the properties are properly displayed.
    }

    /**
     * Test Edit
     */
    public function testEdit(): void
    {
        $expectedStatusCode = 200;
        $userEmail = 'comment_edit_user@example.com';
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], $userEmail);
        $this->client->loginUser($adminUser);
        $category = $this->createCategory();
        $post = $this->createPost($adminUser, $category);
        $fixture = $this->createComment($post);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));
        $result = $this->client->getResponse();

        $this->client->submitForm('Update', [
            'comment[commentText]' => 'Something New',
            'comment[autor]' => 'author@example.com',
            'comment[post]' => $post->getId(),
        ]);

        $fixture = $this->repository->findAll();

        $this->assertEquals('Something New', $fixture[0]->getCommentText());
        $this->assertEquals('author@example.com', $fixture[0]->getAutor());
        $this->assertEquals($expectedStatusCode, $result->getStatusCode());
        $this->assertResponseRedirects('/comment/');
    }
}
