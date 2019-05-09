<?php declare(strict_types = 1);

namespace Apitte\Middlewares\RequestHandler;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{

	/** @var MiddlewareInterface[] */
	private $middlewares;

	/** @var int */
	private $usedCount = 0;

	/**
	 * @param MiddlewareInterface[] $middlewares
	 */
	public function __construct(array $middlewares)
	{
		if ($this->middlewares === []) {
			throw new InvalidStateException('At least one middleware is required.');
		}

		$this->middlewares = $middlewares;
	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$resolved = $this->resolve(0);
		return $resolved->handle($request);
	}

	private function resolve(int $index): RequestHandlerInterface
	{
		return new CallbackRequestHandler(function (ServerRequestInterface $request) use ($index): ResponseInterface {
			$middleware = $this->middlewares[$index];

			$this->usedCount++;
			return $middleware->process($request, $this->resolve(++$index));
		});
	}

	public function getUsedCount(): int
	{
		return $this->usedCount;
	}

	/**
	 * @return MiddlewareInterface[]
	 */
	public function getAll(): array
	{
		return $this->middlewares;
	}

}
