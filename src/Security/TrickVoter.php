<?php

namespace App\Security;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TrickVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Trick) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Trick $trick */
        $trick = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($trick, $user);
            case self::DELETE:
                return $this->canDelete($trick, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Trick $trick, User $user): bool
    {
        if ($this->canDelete($trick, $user)) {
            return true;
        }
        
        return $trick->getUsersWhiteList()->contains($user);
    }

    private function canDelete(Trick $trick, User $user): bool
    {
        return $user === $trick->getAuthor();
    }
}
