<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AdminCrudTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testProductListLoads(): void
    {
        $client = static::createClient();
        $admin = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($admin);

        $client->request('GET', '/admin/product');

        self::assertResponseIsSuccessful();
    }

    public function testCategoryListLoads(): void
    {
        $client = static::createClient();
        $admin = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($admin);

        $client->request('GET', '/admin/category');

        self::assertResponseIsSuccessful();
    }

    public function testOrderListLoads(): void
    {
        $client = static::createClient();
        $admin = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($admin);

        $client->request('GET', '/admin/order');

        self::assertResponseIsSuccessful();
    }

    public function testUserListLoads(): void
    {
        $client = static::createClient();
        $admin = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($admin);

        $client->request('GET', '/admin/user');

        self::assertResponseIsSuccessful();
    }

    public function testAddressListLoads(): void
    {
        $client = static::createClient();
        $admin = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($admin);

        $client->request('GET', '/admin/address');

        self::assertResponseIsSuccessful();
    }

    public function testOrderCreationIsDisabled(): void
    {
        $client = static::createClient();
        $admin = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($admin);

        $client->request('GET', '/admin/order/new');

        self::assertResponseStatusCodeSame(403);
    }

}
