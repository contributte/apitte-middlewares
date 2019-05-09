<?php declare(strict_types = 1);

namespace Apitte\Middlewares\DI;

use Apitte\Core\DI\Plugin\Plugin;
use Apitte\Middlewares\Bridge\Tracy\MiddlewarePanel;
use Apitte\Middlewares\Middleware\AutoBasePathMiddleware;
use Apitte\Middlewares\Middleware\DispatcherMiddleware;
use Apitte\Middlewares\Middleware\HttpMethodOverrideMiddleware;
use Apitte\Middlewares\MiddlewareApplication;
use Apitte\Middlewares\RequestHandler\RequestHandler;
use Nette\DI\ServiceDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;
use Tracy\Bar;

/**
 * @property-read stdClass $config
 */
class MiddlewaresPlugin extends Plugin
{

	public const MIDDLEWARE_TAG = 'apitte.middleware';

	public static function getName(): string
	{
		return 'middlewares';
	}

	protected function getConfigSchema(): Schema
	{
		return Expect::structure([
			'autoBasePath' => Expect::bool(true),
			'methodOverride' => Expect::bool(true),
		]);
	}

	public function loadPluginConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$globalConfig = $this->compiler->getExtension()->getConfig();
		$config = $this->config;

		$applicationDefinition = $builder->getDefinition($this->extensionPrefix('core.application'));
		assert($applicationDefinition instanceof \Nette\DI\Definitions\ServiceDefinition);
		$applicationDefinition->setFactory(MiddlewareApplication::class);

		if ($config->autoBasePath) {
			$builder->addDefinition($this->prefix('middleware.autoBasePath'))
				->setFactory(AutoBasePathMiddleware::class)
				->addTag(self::MIDDLEWARE_TAG, ['priority' => 200]);
		}

		if ($config->methodOverride) {
			$builder->addDefinition($this->prefix('middleware.methodOverride'))
				->setFactory(HttpMethodOverrideMiddleware::class)
				->addTag(self::MIDDLEWARE_TAG, ['priority' => 500]);
		}

		$handler = $builder->addDefinition($this->prefix('requestHandler'))
			->setFactory(RequestHandler::class)
			->setType(RequestHandlerInterface::class);

		if ($globalConfig->debug) {
			$builder->addDefinition($this->prefix('debug.panel'))
				->setFactory(MiddlewarePanel::class, [$handler]);
		}
	}

	public function beforePluginCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$middlewareDefinitions = $builder->findByType(MiddlewareInterface::class);

		// Sort middlewares by priority
		uasort($middlewareDefinitions, static function (ServiceDefinition $d1, ServiceDefinition $d2) {
			$t1 = $d1->getTag(self::MIDDLEWARE_TAG) ?? [];
			$t2 = $d2->getTag(self::MIDDLEWARE_TAG) ?? [];

			$p1 = $t1['priority'] ?? 10;
			$p2 = $t2['priority'] ?? 10;

			if ($p1 === $p2) {
				return 0;
			}

			return ($p1 < $p2) ? -1 : 1;
		});

		// Drop keys, request handler needs numeric indexes
		$middlewareDefinitions = array_values($middlewareDefinitions);

		// Register dispatcher wrapper as last middleware
		$dispatcherMiddlewareDefinition = $builder->addDefinition('middleware.dispatcher')
			->setFactory(DispatcherMiddleware::class);
		$middlewareDefinitions[] = $dispatcherMiddlewareDefinition;

		// Add middlewares to request handler
		$requestHandlerDefinition = $builder->getDefinition($this->prefix('requestHandler'));
		assert($requestHandlerDefinition instanceof \Nette\DI\Definitions\ServiceDefinition);
		$requestHandlerDefinition->setArguments([$middlewareDefinitions]);
	}

	public function afterPluginCompile(ClassType $class): void
	{
		$builder = $this->getContainerBuilder();
		$globalConfig = $this->compiler->getExtension()->getConfig();

		if ($globalConfig->debug) {
			$initialize = $class->getMethod('initialize');
			$initialize->addBody(
				'$this->getService(?)->addPanel($this->getService(?));',
				[$builder->getByType(Bar::class), $this->prefix('debug.panel')]
			);
		}
	}

}
