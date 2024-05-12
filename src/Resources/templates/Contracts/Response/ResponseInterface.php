<?php
declare(strict_types=1);

namespace App\Contracts\Response;

interface ResponseInterface
{
	public function redirectToUrl(string $url);


	public function redirectToRoute(string $route, array $parameters = []);


	public function render(string $view, array $parameters = []);


	public function json(array $data, int $status = 200, array $headers = [], bool $json = false);


	public function jsonError(array $data, int $status = 400, array $headers = [], bool $json = false);


	public function file(string $file, string $filename, array $headers = []);


	public function empty(int $status = 204, array $headers = []);


	public function error(string $view, array $parameters = [], int $status = 500);
}
