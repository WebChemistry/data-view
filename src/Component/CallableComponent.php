<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\UI\Control;
use WebChemistry\DataView\Component\Template\CallableComponentTemplate;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 * @extends BaseViewComponent<T>
 */
final class CallableComponent extends BaseViewComponent
{

	/** @var callable(DataViewComponent<T>): Control */
	private $factory;

	/**
	 * @param callable(DataViewComponent<T>): Control $factory
	 */
	public function __construct(callable $factory)
	{
		$this->factory = $factory;
	}

	protected function createComponentCallable(): Control
	{
		return ($this->factory)($this->getDataView());
	}

	public function render(): void
	{
		/** @var CallableComponentTemplate $template */
		$template = $this->createTemplate();
		$template->setFile(__DIR__ . '/templates/callable/default.latte');
		$template->name = 'callable';

		$template->render();
	}

	public function formatTemplateClass(): string
	{
		return CallableComponentTemplate::class;
	}

}
