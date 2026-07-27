<?php

declare(strict_types=1);

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\Order;
use App\Entity\User;

final class OrderVoter extends Voter
{
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {

        return $attribute === self::VIEW && $subject instanceof Order;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }
        /** @var Order $subject */


        return $subject->getCustomer()?->getId() === $user->getId();
        }

}



