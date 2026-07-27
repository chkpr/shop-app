<?php

declare(strict_types=1);

namespace App\Tests\Security\Voter;

use App\Entity\Order;
use App\Entity\User;
use App\Security\Voter\OrderVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class OrderVoterTest extends TestCase
{
    public function testOwnerCanView(): void
    {
        $user = $this->makeUser(1);

        $order = new Order();
        $order->setCustomer($user);

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $voter = new OrderVoter();
        $result = $voter->vote($token, $order, ['VIEW']);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testOtherUserCannotView(): void
    {
        $owner = $this->makeUser(1);
        $intruder = $this->makeUser(2);

        $order = new Order();
        $order->setCustomer($owner);

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($intruder);

        $voter = new OrderVoter();
        $result = $voter->vote($token, $order, ['VIEW']);

        self::assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    private function makeUser(int $id): User
    {
        $user = new User();
        $ref = new \ReflectionProperty(User::class, 'id');
        $ref->setValue($user, $id);

        return $user;
    }
}
