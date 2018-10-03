<?php declare(strict_types = 1);

namespace Apitte\Middlewares;

use Apitte\Core\Dispatcher\IDispatcher;
use Contributte\Middlewares\IMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApiMiddleware implements IMiddleware
{

	/** @var IDispatcher */
	protected $dispatcher;

	public function __construct(IDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		// Pass this API request/response objects to API dispatcher
		$response = $this->dispatcher->dispatch($request, $response);

		// Pass response to next middleware
		$response = $next($request, $response);

		return $response;
	}

}
