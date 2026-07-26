<?php


declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AdminAccessTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testAnonymousIsRedirected(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        self::assertResponseRedirects();
    }

    public function regularUserIsDenied():void
    {
        $client = static::createClient();

        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);
        $client->loginUser($user);

        $client->request('GET', '/admin');

        self::assertResponseStatusCodeSame(403);
    }

    public function testAdminHasAccess(): void
    {
        $client = static::createClient();

        $admin = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($admin);
        $client->request('GET', '/admin');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
    }


}
