<?php declare(strict_types = 1);

namespace Apitte\Middlewares\Middleware;

use Apitte\Core\Http\ApiResponse;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BasicAuthMiddleware implements MiddlewareInterface
{

	public const ATTR_USERNAME = 'username';

	/** @var string */
	private $title;

	/** @var mixed[] */
	private $users = [];

	public function __construct(string $title = 'Restricted zone')
	{
		$this->title = $title;
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$authorization = $this->parseAuthorizationHeader($request->getHeaderLine('Authorization'));
		if ($authorization !== null && $this->auth($authorization['username'], $authorization['password'])) {
			$request = $request->withAttribute(self::ATTR_USERNAME, $authorization['username']);
			return $handler->handle($request);
		}

		$response = new ApiResponse(new Response());
		return $response
			->withStatus(401)
			->withHeader('WWW-Authenticate', 'Basic realm="' . $this->title . '"');
	}

	/**
	 * @return static
	 */
	public function addUser(string $user, string $password, bool $unsecured = false): self
	{
		$this->users[$user] = [
			'password' => $password,
			'unsecured' => $unsecured,
		];

		return $this;
	}

	protected function auth(string $user, string $password): bool
	{
		return !(
			!isset($this->users[$user]) ||
			($this->users[$user]['unsecured'] === true && !hash_equals($password, $this->users[$user]['password'])) ||
			($this->users[$user]['unsecured'] === false && !password_verify($password, $this->users[$user]['password']))
		);
	}

	/**
	 * @return mixed[]|null
	 */
	protected function parseAuthorizationHeader(string $header): ?array
	{
		if (strpos($header, 'Basic') !== 0) {
			return null;
		}

		$header = explode(':', (string) base64_decode(substr($header, 6), true), 2);
		return [
			'username' => $header[0],
			'password' => $header[1] ?? null,
		];
	}

}
