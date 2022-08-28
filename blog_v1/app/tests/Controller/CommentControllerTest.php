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

    public function testIndex(): void
    {
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'comment_admin1@example.com');
        $this->client->loginUser($adminUser);
        $crawler = $this->client->request('GET', $this->path);
        $result = $this->client->getResponse();

        $this->assertEquals($expectedStatusCode, $result->getStatusCode());

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Comment index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

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

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'comment[comment_text]' => 'Testing',
            'comment[autor]' => $userEmail,
            'comment[post]' => $post->getId(),
        ]);

        self::assertResponseRedirects('/comment/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
        $this->assertEquals($expectedStatusCode, $result->getStatusCode());
        $this->assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $expectedStatusCode = 200;
        $userEmail = 'comment_show_user@example.com';
        $adminUser = $this->createUser([UserRole::ROLE_USER->value], $userEmail);
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

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Comment');

        $this->assertEquals($expectedStatusCode, $result->getStatusCode());

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $expectedStatusCode = 200;
        $userEmail = 'comment_edit_user@example.com';
        $adminUser = $this->createUser([UserRole::ROLE_USER->value], $userEmail);
        $this->client->loginUser($adminUser);
        $category = $this->createCategory();
        $post = $this->createPost($adminUser, $category);
        $fixture = $this->createComment($post);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));
        $result = $this->client->getResponse();

        $this->client->submitForm('Update', [
            'comment[comment_text]' => 'Something New',
            'comment[autor]' => 'New Author',
            'comment[post]' => $post->getId(),
        ]);

        $this->assertEquals($expectedStatusCode, $result->getStatusCode());

        self::assertResponseRedirects('/comment/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCommentText());
        self::assertSame('New Author', $fixture[0]->getAutor());
    }

    public function testRemove(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $expectedStatusCode = 200;
        $userEmail = 'comment_remove_user@example.com';
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], $userEmail);
        $this->client->loginUser($adminUser);
        $category = $this->createCategory();
        $post = $this->createPost($adminUser, $category);
        $fixture = $this->createComment($post);
        echo $originalNumObjectsInRepository;
        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', $this->path . '/' . $fixture->getId() . '/delete');
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/comment/');

        $result = $this->client->getResponse();
        $this->assertEquals(200, $result->getStatusCode());
    }
}
