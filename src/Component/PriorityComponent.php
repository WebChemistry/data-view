<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\UI\Control;

/**
 * @template T
 * @extends BaseViewComponent<T>
 */
final class PriorityComponent extends BaseViewComponent
{

	/**
	 * @param Control $control
	 */
	public function __construct(
		private Control $control,
		int $renderPriority,
	)
	{
		$this->setRenderPriority($renderPriority);
	}

	protected function createComponentPriority(): Control
	{
		return $this->control;
	}

	public function render(): void
	{
		$template = $this->createTemplate();
		$template->setFile(__DIR__ . '/templates/priority/default.latte');

		$template->render();
	}

}
