<?php

namespace App\Security\Voter;

use App\Entity\Product;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ProductVoter extends Voter
{
    const EDIT = 'PRODUCT_EDIT';
    const DELETE = 'PRODUCT_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $product): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$product instanceof Product) {
            return false;
        }

        return true;
    }

    protected voteOnAttribute(string $attribute, $product, TokenInterface $token): bool
    {
        // On récupère l'utilisateur à partir du token
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // On vérifie si l'utilisateur dispose du rôle ROLE_ADMIN
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // On vérifie les permissions
        switch ($attribute) {
            case self::EDIT:
                // On vérifie si l'utilisateur peut éditer le produit
                return $this->canEdit();
                break;
            case self::DELETE:
                // On vérifie si l'utilisateur peut supprimer le produit
                return $this->canDelete();
                break;
        }
    }

    private function canEdit(): bool
    {
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }

    private function canDelete(): bool
    {
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
}
