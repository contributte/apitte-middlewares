<?php declare(strict_types = 1);

namespace Apitte\Middlewares;

use Apitte\Core\Application\BaseApplication;
use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareApplication extends BaseApplication
{

	/** @var RequestHandlerInterface */
	private $requestHandler;

	public function __construct(IErrorHandler $errorHandler, RequestHandlerInterface $requestHandler)
	{
		parent::__construct($errorHandler);
		$this->requestHandler = $requestHandler;
	}

	protected function dispatch(ApiRequest $request): ApiResponse
	{
		$response = $this->requestHandler->handle($request);

		if (!$response instanceof ApiResponse) {
			$response = new ApiResponse($response);
		}

		return $response;
	}

}
