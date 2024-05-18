<?php
declare(strict_types=1);

namespace App\VO\Null;

use App\Contracts\Security\UserInterface;

final class NullUser implements UserInterface
{
	public static function create(): self
	{
		return new self();
	}


	public function getRoles()
	{
		// TODO: Implement getRoles() method.
	}


	public function getPassword()
	{
		// TODO: Implement getPassword() method.
	}


	public function getSalt()
	{
		// TODO: Implement getSalt() method.
	}


	public function getUsername()
	{
		// TODO: Implement getUsername() method.
	}


	public function eraseCredentials()
	{
		// TODO: Implement eraseCredentials() method.
	}

    public function identifier(): string
    {
        return 'null user';
    }
}
