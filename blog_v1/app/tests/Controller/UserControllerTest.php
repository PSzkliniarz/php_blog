<?php
/**
 * User controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Tests\BaseTest;
use App\Repository\UserRepository;

/**
 * Class UserControllerTest.
 */
class UserControllerTest extends BaseTest
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/user';

    /**
     * @var UserRepository
     */
    private UserRepository $repository;

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(User::class);
    }


    /**
     * Test '/login' route.
     */
    public function testLoginRoute(): void
    {
        // given
        $expectedStatusCode = 200;

        // when
        $this->httpClient->request('GET', '/login');

        // then
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals($expectedStatusCode, $resultHttpStatusCode);
    }

    /**
     * Test '/logout' route.
     */
    public function testLogoutRoute(): void
    {
        // given
        $expectedStatusCode = 302;

        // when
        $this->httpClient->request('GET', '/logout');

        // then
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals($expectedStatusCode, $resultHttpStatusCode);
    }



    /**
     * Test edit users route for normal user.
     */
    public function testEditRouteNormalUser(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value], 'edit_user1@example.com');
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', "/user/".strval($user->getId())."/password_edit");
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultStatusCode);
    }

    /**
     * Test edit users route for admin user.
     */
    public function testEditRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'user_admin@example.com');
        $this->httpClient->loginUser($adminUser);


        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', "/user/".strval($adminUser->getId())."/password_edit");
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }
}
