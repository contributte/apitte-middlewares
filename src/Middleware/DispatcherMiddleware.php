<?php declare(strict_types = 1);

namespace Apitte\Middlewares\Middleware;

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatcherMiddleware implements MiddlewareInterface
{

	/** @var IDispatcher */
	private $dispatcher;

	public function __construct(IDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if (!$request instanceof ApiRequest) {
			$request = new ApiRequest($request);
		}

		return $this->dispatcher->dispatch($request, new ApiResponse(new Response()));
	}

}
