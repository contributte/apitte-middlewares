<?php declare(strict_types = 1);

namespace Apitte\Middlewares\DI;

use Apitte\Core\DI\Plugin\AbstractPlugin;
use Apitte\Core\DI\Plugin\PluginCompiler;
use Apitte\Middlewares\ApiMiddleware;
use Contributte\Middlewares\AutoBasePathMiddleware;
use Contributte\Middlewares\DI\MiddlewaresExtension;
use Contributte\Middlewares\TracyMiddleware;

class MiddlewaresPlugin extends AbstractPlugin
{

	public const PLUGIN_NAME = 'middlewares';

	/** @var mixed[] */
	protected $defaults = [
		'tracy' => true,
		'autobasepath' => true,
	];

	public function __construct(PluginCompiler $compiler)
	{
		parent::__construct($compiler);
		$this->name = self::PLUGIN_NAME;
	}

	/**
	 * Register services (middlewares wrappers)
	 */
	public function loadPluginConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$global = $this->compiler->getExtension()->getConfig();
		$config = $this->getConfig();

		if ($config['tracy'] === true) {
			$builder->addDefinition($this->prefix('tracy'))
				->setFactory(TracyMiddleware::class . '::factory', [$global['debug']])
				->addTag(MiddlewaresExtension::MIDDLEWARE_TAG, ['priority' => 100]);
		}

		if ($config['autobasepath'] === true) {
			$builder->addDefinition($this->prefix('autobasepath'))
				->setFactory(AutoBasePathMiddleware::class)
				->addTag(MiddlewaresExtension::MIDDLEWARE_TAG, ['priority' => 200]);
		}

		$builder->addDefinition($this->prefix('api'))
			->setFactory(ApiMiddleware::class)
			->addTag(MiddlewaresExtension::MIDDLEWARE_TAG, ['priority' => 500]);
	}

}
