<?php declare(strict_types = 1);

namespace Apitte\Middlewares;

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ApiMiddleware
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
		$response = $this->dispatch($request, $response);

		// Pass response to next middleware
		$response = $next($request, $response);

		return $response;
	}

	protected function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		try {
			// Pass to dispatcher, find handler, process some logic and return response.
			$response = $this->dispatcher->dispatch($request, $response);

			// Validate returned api response
			if (!($response instanceof ResponseInterface)) {
				throw new InvalidStateException(sprintf('Returned response must be type of %s', ResponseInterface::class));
			}

			return $response;
		} catch (Throwable $e) {
			// Just throw this out
			throw $e;
		}
	}

}
