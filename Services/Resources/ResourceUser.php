<?php

namespace Services\Resources;

use Services\Resource;

class ResourceUser extends Resource
{

    public static function toArray($user)
    {
        return [
            'id' => $user->getId(),
            'nome' => $user->getName(),
            'email' => $user->getEmail()
        ];
    }
}
