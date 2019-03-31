<?php declare(strict_types = 1);

/**
 * Test: ApiMiddleware
 */

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Middlewares\ApiMiddleware;
use Contributte\Middlewares\Utils\Lambda;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Ninjify\Nunjuck\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = Mockery::mock(IDispatcher::class);
	$dispatcher->shouldReceive('dispatch')
		->once()
		->with($request, $response)
		->andReturn($response);

	$middleware = new ApiMiddleware($dispatcher);
	$returned = $middleware($request, $response, Lambda::leaf());

	Assert::type($response, $returned);
	Assert::same($response, $returned);
});
