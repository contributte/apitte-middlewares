<?php declare(strict_types = 1);

namespace Apitte\Middlewares\Bridge\Tracy;

use Apitte\Middlewares\RequestHandler\RequestHandler;
use Tracy\IBarPanel;

class MiddlewarePanel implements IBarPanel
{

	/** @var RequestHandler */
	private $handler;

	public function __construct(RequestHandler $handler)
	{
		$this->handler = $handler;
	}

	public function getTab(): ?string
	{
		if ($this->handler->getUsedCount() === 0) {
			return null;
		}

		ob_start();
		require __DIR__ . '/templates/tab.phtml';
		return (string) ob_get_clean();
	}

	public function getPanel(): ?string
	{
		$usedCount = $this->handler->getUsedCount();
		$middlewares = $this->handler->getAll();

		if ($usedCount === 0) {
			return null;
		}

		ob_start();
		require __DIR__ . '/templates/panel.phtml';
		return (string) ob_get_clean();
	}

}
