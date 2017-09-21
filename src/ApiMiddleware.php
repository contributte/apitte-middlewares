<?php

namespace Apitte\Middlewares;

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApiMiddleware
{

	/** @var IDispatcher */
	protected $dispatcher;

	/**
	 * @param IDispatcher $dispatcher
	 */
	public function __construct(IDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * MIDDLEWARE **************************************************************
	 */

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param callable $next
	 * @return ResponseInterface
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
	{
		// Create API request & response
		$apiRequest = $this->createApiRequest($request, $response);
		$apiResponse = $this->createApiResponse($request, $response);

		// Pass this API request/response objects to API dispatcher
		$response = $this->dispatch($apiRequest, $apiResponse);

		// Pass response to next middleware
		$response = $next($request, $response);

		return $response;
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	protected function dispatch(ServerRequestInterface $request, ResponseInterface $response)
	{
		try {
			// Pass to dispatcher, find handler, process some logic and return response.
			$response = $this->dispatcher->dispatch($request, $response);

			// Validate returned api response
			if (!($response instanceof ResponseInterface)) {
				throw new InvalidStateException(sprintf('Returned response must be type of %s', ApiResponse::class));
			}

			return $response;
		} catch (Exception $e) {
			// Just throw this out
			throw $e;
		}
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @param ServerRequestInterface $psr7Request
	 * @param ResponseInterface $psr7Response
	 * @return ServerRequestInterface
	 */
	protected function createApiRequest(ServerRequestInterface $psr7Request, ResponseInterface $psr7Response)
	{
		return $psr7Request;
	}

	/**
	 * @param ServerRequestInterface $psr7Request
	 * @param ResponseInterface $psr7Response
	 * @return ResponseInterface
	 */
	protected function createApiResponse(ServerRequestInterface $psr7Request, ResponseInterface $psr7Response)
	{
		return $psr7Response;
	}

}
