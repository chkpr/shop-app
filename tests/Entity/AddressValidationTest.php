<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddressValidationTest extends KernelTestCase
{
    public function testBlankAddressIsInvalid(): void
    {
        self::bootKernel();
        $validator = static::getContainer()->get('validator');

        $address = new Address();

        $errors = $validator->validate($address);

        //une adresse vide génère une erreur
        self::assertGreaterThan(0, count($errors));
    }

    public function testValidAddressIsValid(): void
    {
        self::bootkernel();
        $validator = static::getContainer()->get('validator');

        $address = new Address();
        $address->setFullName('Jean Dupont');
        $address->setStreet('12 rue de Montreuil');
        $address->setPostalCode('75011');
        $address->setCity('Paris');
        $address->setCountry('France');

        $errors = $validator->validate($address);

        self::assertCount(0, $errors);

    }
}
