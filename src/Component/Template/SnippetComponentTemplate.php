<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Template;

use Nette\Bridges\ApplicationLatte\Template;
use WebChemistry\DataView\Component\SnippetComponent;

/**
 * @template T
 */
final class SnippetComponentTemplate extends Template
{

	/** @var SnippetComponent<T> */
	public SnippetComponent $control;

	public string $name;

	/** @var array<string, string> */
	public array $attributes = [];

}
