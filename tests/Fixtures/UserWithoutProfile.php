<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures;

use Illuminate\Auth\GenericUser;

/**
 * A user from an application that handles theme persistence on its own.
 */
class UserWithoutProfile extends GenericUser
{
    public function __construct()
    {
        parent::__construct(['id' => 2]);
    }
}
