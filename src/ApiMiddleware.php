<?php

namespace Apitte\Middlewares;

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
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
	 * @return ApiResponse
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
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	protected function dispatch(ApiRequest $request, ApiResponse $response)
	{
		try {
			// Pass to dispatcher, find handler, process some logic and return response.
			$response = $this->dispatcher->dispatch($request, $response);

			// Validate returned api response
			if (!($response instanceof ApiResponse)) {
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
	 * @return ApiRequest
	 */
	protected function createApiRequest(ServerRequestInterface $psr7Request, ResponseInterface $psr7Response)
	{
		if ($psr7Request instanceof ApiRequest) return $psr7Request;

		return ApiRequest::of($psr7Request);
	}

	/**
	 * @param ServerRequestInterface $psr7Request
	 * @param ResponseInterface $psr7Response
	 * @return ApiResponse
	 */
	protected function createApiResponse(ServerRequestInterface $psr7Request, ResponseInterface $psr7Response)
	{
		if ($psr7Response instanceof ApiResponse) return $psr7Response;

		return ApiResponse::of($psr7Response);
	}

}
