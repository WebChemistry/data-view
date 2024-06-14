<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Template;

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

	/** @var int[] */
	public array $steps;

	public ?string $nextLink;

	public ?string $previousLink;

	public bool $ajax;

	/** @var callable(int $page): ?string */
	public $stepLink;

}
