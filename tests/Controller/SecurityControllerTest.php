<?php

namespace App\Tests\Controller;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecurityControllerTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testLoginPageLoads():void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
    }

    public function testUserCanLogin():void
    {
        $client = static::createClient();

        //Créer un utilisateur de test - mot de passe haché automatiquement
        $user = UserFactory::createOne(['email' => 'login-test@example.com']);

        //aller sur la page de login
        $client->request('GET', '/login');

        //remplir et soumettre le formulaire
        $client->submitForm('Sign in', [
            '_username' => 'login-test@example.com',
            '_password' => 'password',
        ]);

        //après login réussi, on est redirigé
        self::assertResponseRedirects();
    }
}
