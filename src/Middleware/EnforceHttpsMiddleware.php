<?php declare(strict_types = 1);

namespace Apitte\Middlewares\Middleware;

use Apitte\Core\Exception\Api\ClientErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EnforceHttpsMiddleware implements MiddlewareInterface
{

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if (strtolower($request->getUri()->getScheme()) !== 'https') {
			throw ClientErrorException::create()
				->withCode(400)
				->withMessage('Encrypted connection is required. Please use https protocol.');
		}

		return $handler->handle($request);
	}

}
