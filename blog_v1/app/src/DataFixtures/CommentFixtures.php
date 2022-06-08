<?php
/**
 * comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Enum\TaskStatus;
use App\Entity\Tag;
use App\Entity\Comment;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures.
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullPropertyFetch
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(100, 'comments', function (int $i) {
            $comment = new Comment();
            $comment->setCommentText($this->faker->sentence);
            $comment->setAutor($this->faker->email);

            /** @var Post $post */
            $post = $this->getRandomReference('posts');
            $comment->setPost($post);

            return $comment;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: CategoryFixtures::class}
     */
    public function getDependencies(): array
    {
        return [PostFixtures::class];
    }
}
