<?php declare(strict_types = 1);

namespace Apitte\Middlewares\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpMethodOverrideMiddleware implements MiddlewareInterface
{

	public const OVERRIDE_HEADER = 'X-HTTP-Method-Override';

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if ($request->hasHeader(self::OVERRIDE_HEADER) && $request->getHeader(self::OVERRIDE_HEADER)[0] !== '') {
			$request = $request->withMethod($request->getHeader(self::OVERRIDE_HEADER)[0]);
		}

		return $handler->handle($request);
	}

}
