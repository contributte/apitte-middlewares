<?php

namespace Apitte\Middlewares\DI;

use Apitte\Core\DI\AbstractPlugin;
use Apitte\Core\DI\ApiExtension;
use Apitte\Middlewares\ApiMiddleware;
use Contributte\Middlewares\AutoBasePathMiddleware;
use Contributte\Middlewares\DI\MiddlewaresExtension;
use Contributte\Middlewares\TracyMiddleware;
use Nette\DI\Statement;
use RuntimeException;

class MiddlewaresPlugin extends AbstractPlugin
{

	const PLUGIN_NAME = 'middlewares';

	/**
	 * @param ApiExtension $extension
	 */
	public function __construct(ApiExtension $extension)
	{
		parent::__construct($extension);
		$this->name = self::PLUGIN_NAME;
	}

	/**
	 * Register services (middlewares wrapper)
	 *
	 * @return void
	 */
	public function loadPluginConfiguration()
	{
		// Is MiddlewaresExtension (contributte/middlewares) registered?
		if (!$this->getMiddlewaresExtension()) {
			throw new RuntimeException(sprintf('Extension %s is not registered', MiddlewaresExtension::class));
		}

		// HACK! Update middlewares extension
		$extension = $this->getMiddlewaresExtension();

		$extension->setConfig([
			'middlewares' => [
				new Statement(TracyMiddleware::class . '::factory', [TRUE]),
				new Statement(AutoBasePathMiddleware::class),
				new Statement(ApiMiddleware::class),
			],
		]);
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @return MiddlewaresExtension
	 */
	protected function getMiddlewaresExtension()
	{
		$ext = $this->extension->getCompiler()->getExtensions(MiddlewaresExtension::class);

		return $ext ? reset($ext) : NULL;
	}

}
