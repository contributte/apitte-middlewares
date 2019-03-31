<?php declare(strict_types = 1);

namespace Apitte\Middlewares;

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
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
		if (!$request instanceof ApiRequest) {
			$request = new ApiRequest($request);
		}

		if (!$response instanceof ApiResponse) {
			$response = new ApiResponse($response);
		}

		// Pass this API request/response objects to API dispatcher
		$response = $this->dispatcher->dispatch($request, $response);

		// Pass response to next middleware
		$response = $next($request, $response);

		return $response;
	}

}
