<?php

namespace bTokman\Tests;

use Symfony\Component\Validator\Constraints\{
    Email, Length, NotBlank
};

class ValidationObject extends BaseObject
{
    public $validationRules = [
        'email' => [Email::class, NotBlank::class],
        'password' => [NotBlank::class, [Length::class, ['min' => 8]]],
    ];

    public $email;

    public $password;
}
