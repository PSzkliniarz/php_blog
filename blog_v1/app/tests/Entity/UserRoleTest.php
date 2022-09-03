<?php

namespace App\Tests\Entity\Enum;

use App\Entity\Enum\UserRole;
use PHPUnit\Framework\TestCase;

/**
 * User Role test class
 */
class UserRoleTest extends TestCase
{
    /**
     * Test user label
     *
     * @return void
     */
    public function testLabel()
    {
        $this->assertEquals('label.role_user', UserRole::ROLE_USER->label());
        $this->assertEquals('label.role_user', UserRole::ROLE_USER->label());
    }
}