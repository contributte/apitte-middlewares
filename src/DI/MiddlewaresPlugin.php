<?php

namespace Apitte\Middlewares\DI;

use Apitte\Core\DI\Plugin\AbstractPlugin;
use Apitte\Core\DI\Plugin\PluginCompiler;
use Apitte\Middlewares\ApiMiddleware;
use Contributte\Middlewares\AutoBasePathMiddleware;
use Contributte\Middlewares\DI\MiddlewaresExtension;
use Contributte\Middlewares\TracyMiddleware;

class MiddlewaresPlugin extends AbstractPlugin
{

	const PLUGIN_NAME = 'middlewares';

	/** @var array */
	protected $defaults = [
		'tracy' => TRUE,
		'autobasepath' => TRUE,
	];

	/**
	 * @param PluginCompiler $compiler
	 */
	public function __construct(PluginCompiler $compiler)
	{
		parent::__construct($compiler);
		$this->name = self::PLUGIN_NAME;
	}

	/**
	 * Process and validate config
	 *
	 * @param array $config
	 * @return void
	 */
	public function setupPlugin(array $config = [])
	{
		$this->setupConfig($this->defaults, $config);
	}

	/**
	 * Register services (middlewares wrapper)
	 *
	 * @return void
	 */
	public function loadPluginConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$global = $this->compiler->getExtension()->getConfig();
		$config = $this->getConfig();

		if ($config['tracy'] === TRUE) {
			$builder->addDefinition($this->prefix('tracy'))
				->setFactory(TracyMiddleware::class . '::factory', [$global['debug']])
				->addTag(MiddlewaresExtension::MIDDLEWARE_TAG, ['priority' => 100]);
		}

		if ($config['autobasepath'] === TRUE) {
			$builder->addDefinition($this->prefix('autobasepath'))
				->setFactory(AutoBasePathMiddleware::class)
				->addTag(MiddlewaresExtension::MIDDLEWARE_TAG, ['priority' => 200]);
		}

		$builder->addDefinition($this->prefix('api'))
			->setFactory(ApiMiddleware::class)
			->addTag(MiddlewaresExtension::MIDDLEWARE_TAG, ['priority' => 500]);
	}

}
