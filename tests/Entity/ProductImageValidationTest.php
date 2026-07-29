<?php


declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductImageValidationTest extends KernelTestCase
{
    public function testFakeImageIsRejected(): void
    {
        self::bootKernel();
        $validator = static::getContainer()->get('validator');

        $product = new Product();

        // un fichier .jpg qui contient en réalité du PHP
        $fakeImage = new UploadedFile(
            __DIR__ . '/../fixtures/fake.jpg',
            'fake.jpg',
            'image/jpeg',      // type MIME déclaré (mensonger)
            null,
            true               // mode test
        );

        $product->setImageFile($fakeImage);

        $errors = $validator->validate($product);

        // le validateur doit détecter que ce n'est pas une vraie image
        self::assertGreaterThan(0, count($errors));
    }
}
