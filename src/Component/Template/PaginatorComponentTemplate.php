<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Template;

use Latte\Attributes\TemplateFunction;
use Nette\Bridges\ApplicationLatte\Template;
use WebChemistry\DataView\Component\PaginatorComponent;

/**
 * @template T
 */
class PaginatorComponentTemplate extends Template
{

	/** @var PaginatorComponent<T> */
	public PaginatorComponent $control;

	public int $page;

	public int $pageCount;

	/** @var int[] */
	public array $steps;

	public ?string $nextLink;

	public ?string $previousLink;

	public bool $ajax;

	#[TemplateFunction]
	public function stepLink(int $step): string
	{
		return $this->control->stepLink($step);
	}

}
