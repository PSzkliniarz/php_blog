<?php

namespace App\Tests;

use App\Entity\Category;
//use App\Entity\Operation;
//use App\Entity\Payment;
//use App\Entity\Tag;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
//use App\Entity\Wallet;
use App\Repository\CategoryRepository;
//use App\Repository\OperationRepository;
//use App\Repository\PaymentRepository;
//use App\Repository\TagRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
//use App\Repository\WalletRepository;
use DateTime;
use Monolog\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BaseTest extends WebTestCase
{

    /**
     * Test client.
     */
    protected KernelBrowser $httpClient;

    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity
     */
    protected function createUser(array $roles, string $email): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'p@55w0rd'
            )
        );
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->add($user, true);

        return $user;
    }

    /**
     * Simulate user log in.
     *
     * @param User $user User entity
     */
    protected function logIn(User $user): void
    {
        $session = self::getContainer()->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->httpClient->getCookieJar()->set($cookie);
    }

    /**
     * Create category.
     */
    protected function createCategory(): Category
    {
        $category = new Category();
        $category->setName('CName');
        $category->setUpdatedAt(new DateTime('now'));
        $category->setCreatedAt(new DateTime('now'));
        $categoryRepository = self::getContainer()->get(CategoryRepository::class);
        $categoryRepository->add($category, true);

        return $category;
    }

    /**
     * Create post.
     */
    protected function createPost(User $user, Category $category): Post
    {
        $post = new Post();
        $post->setTitle('PName');
        $post->setContent('PContent');
        $post->setAuthor($user);
        $post->setCategory($category);
        $post->setUpdatedAt(DateTimeImmutable::createFromMutable(new \DateTime('@'.strtotime('now'))));
        $post->setCreatedAt(DateTimeImmutable::createFromMutable(new \DateTime('@'.strtotime('now'))));
        $postRepository = self::getContainer()->get(PostRepository::class);
        $postRepository->add($post, true);

        return $post;
    }

    /**
     * Create comment
     */
    protected function createComment(Post $post): Comment
    {
        $comment = new Comment();
        $comment->setCommentText('CText');
        $comment->setAutor('author@mail.com');
        $comment->setPost($post);

        $commentRepository = self::getContainer()->get(CommentRepository::class);
        $commentRepository->add($comment, true);

        return $comment;

    }
//
//    /**
//     * Create Payment.
//     * @return Payment
//     */
//    protected function createPayment(): Payment
//    {
//        $payment = new Payment();
//        $payment->setName('TPayment');
//        $paymentRepository = self::getContainer()->get(PaymentRepository::class);
//        $paymentRepository->save($payment);
//
//        return $payment;
//    }
//
//    /**
//     * Create Operation.
//     * @return Operation
//     */
//    protected function createOperation(): Operation
//    {
//        $operation = new Operation();
//        $operation->setName('TOperation');
//        $operationRepository = self::getContainer()->get(OperationRepository::class);
//        $operationRepository->save($operation);
//
//        return $operation;
//    }
//
//    /**
//     * Create Tag.
//     * @return Tag
//     */
//    protected function createTag(): Tag
//    {
//        $tag = new Tag();
//        $tag->setName('TTag');
//        $tagRepository = self::getContainer()->get(TagRepository::class);
//        $tagRepository->save($tag);
//
//        return $tag;
//    }
//
//    /**
//     * Create Wallet.
//     * @return Wallet
//     */
//    protected function createWallet(User $user): Wallet
//    {
//        $wallet = new Wallet();
//        $wallet->setName('TWallet');
//        $wallet->setBalance('1000');
//        $wallet->setUser($user);
//        $walletRepository = self::getContainer()->get(WalletRepository::class);
//        $walletRepository->save($wallet);
//
//        return $wallet;
//    }
}