<?php declare(strict_types = 1);

namespace Apitte\Middlewares\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class RequestLoggingMiddleware implements MiddlewareInterface
{

	/** @var LoggerInterface */
	private $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$this->logger->info(sprintf('Requested url: %s', (string) $request->getUri()->withUserInfo('', '')));

		return $handler->handle($request);
	}

}
