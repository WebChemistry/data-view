<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Utils\Html;

/**
 * @template T
 * @extends BaseViewComponent<T>
 */
final class TextualComponent extends BaseViewComponent
{

	private Html $text;

	public function __construct(string|Html $text)
	{
		$this->text =is_string($text) ? Html::fromText($text) : $text;
	}

	public function render(): void
	{
		echo $this->text;
	}

}
