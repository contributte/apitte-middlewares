<?php declare(strict_types = 1);

namespace Apitte\Middlewares\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AutoBasePathMiddleware implements MiddlewareInterface
{

	public const ATTR_ORIGINAL_PATH = 'path.original';
	public const ATTR_BASE_PATH = 'path.base';

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$uri = $request->getUri();
		$basePath = $uri->getPath();

		// Base-path auto detection (inspired in @nette/routing)
		$lpath = strtolower($uri->getPath());
		$serverParams = $request->getServerParams();

		$script = isset($serverParams['SCRIPT_NAME']) ? strtolower($serverParams['SCRIPT_NAME']) : '';
		if ($lpath !== $script) {
			$max = min(strlen($lpath), strlen($script));
			$i = 0;
			while ($i < $max && $lpath[$i] === $script[$i]) {
				$i++;
			}
			// Cut basePath from URL
			// /foo/bar/test => /test
			// (empty) -> /
			$basePath = $i !== 0 ? substr($basePath, 0, (int) strrpos($basePath, '/', $i - strlen($basePath) - 1) + 1) : '/';
		}

		// Try replace path or just use slash (/)
		$pos = strrpos($basePath, '/');
		if ($pos !== false) {
			// Cut base path by last slash (/)
			$basePath = substr($basePath, 0, $pos + 1);
			// Drop part of path (basePath)
			$newPath = substr($uri->getPath(), strlen($basePath));
		} else {
			$newPath = '/';
		}

		// New path always starts with slash (/)
		$newPath = '/' . ltrim($newPath, '/');

		// Update request with new path (fake path) and also provide new attributes
		$request = $request
			->withAttribute(self::ATTR_ORIGINAL_PATH, $uri->getPath())
			->withAttribute(self::ATTR_BASE_PATH, $basePath)
			->withUri($uri->withPath($newPath));

		// Pass to next middleware
		return $handler->handle($request);
	}

}
