<?php declare(strict_types = 1);

namespace Apitte\Middlewares\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallbackRequestHandler implements RequestHandlerInterface
{

	/** @var callable&(callable(ServerRequestInterface $request) : ResponseInterface) */
	private $callback;

	/**
	 * @param callable&(callable(ServerRequestInterface $request) : ResponseInterface) $callback
	 */
	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		return call_user_func($this->callback, $request);
	}

}
