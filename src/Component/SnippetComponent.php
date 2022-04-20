<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\UI\Control;
use WebChemistry\DataView\Component\Template\SnippetComponentTemplate;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 * @extends BaseViewComponent<T>
 */
final class SnippetComponent extends BaseViewComponent
{

	public const TEMPLATES = [
		'default' => __DIR__ . '/templates/snippet/default.latte',
		'naja-append' => __DIR__ . '/templates/snippet/naja-append.latte',
		'naja-prepend' => __DIR__ . '/templates/snippet/naja-prepend.latte',
	];

	public function __construct(
		private Control $control,
		private string $file = __DIR__ . '/templates/snippet/default.latte',
	)
	{
		$this->monitor(DataViewComponent::class, function (DataViewComponent $component): void {
			$component->onRedrawRequest[] = function (): void {
				$this->redraw();
			};
		});
	}

	protected function createComponentSnippet(): Control
	{
		return $this->control;
	}

	public function render(): void
	{
		/** @var SnippetComponentTemplate $template */
		$template = $this->createTemplate();
		$template->setFile($this->file);
		$template->name = 'snippet';

		$template->render();
	}

	public function redraw(): void
	{
		$this->redrawControl();
	}

	public function formatTemplateClass(): string
	{
		return SnippetComponentTemplate::class;
	}

}
