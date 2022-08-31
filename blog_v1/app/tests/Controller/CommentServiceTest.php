<?php
/**
 * Comment Service Test.
 */

namespace App\Tests\Controller;

use App\Entity\Comment;
use App\Entity\Enum\UserRole;
use App\Repository\CommentRepository;
use App\Service\CommentService;
use App\Tests\BaseTest;
use Doctrine\ORM\Exception\ORMException;

/**
 * Class CommentServiceTest.
 * @property $entityManager
 */
class CommentServiceTest extends BaseTest
{
    /**
     * Comment service.
     */
    private ?CommentService $commentService;

    /**
     * @return void void
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->commentService = $container->get(CommentService::class);
    }


    /**
     * Test delete.
     *
     * @throws ORMException
     */
    public function testDelete(): void
    {
        $commentRepository =
            static::getContainer()->get(CommentRepository::class);

        $user= $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'post_admin@example.com');
        $category = $this->createCategory();
        $post = $this->createPost($user, $category);
        $commentToDelete = $this->createComment($post);

        $commentRepository->save($commentToDelete);

        $before = $commentRepository->findAll();

        $this->commentService->delete($commentToDelete);
        $after = $commentRepository->findAll();

        $this->assertEquals(count($before), count($after) + 1);
    }
}