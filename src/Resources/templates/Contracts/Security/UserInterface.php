<?php
declare(strict_types=1);

namespace App\Contracts\Security;

interface UserInterface
{
	public function getRoles();


	public function getPassword();


	public function getSalt();


	public function getUsername();


	public function eraseCredentials();

    public function identifier(): string;
}
