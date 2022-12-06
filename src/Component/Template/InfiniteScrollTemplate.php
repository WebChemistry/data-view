<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Template;

use Nette\Bridges\ApplicationLatte\Template;
use WebChemistry\DataView\Component\InfiniteScrollComponent;

/**
 * @template T
 */
final class InfiniteScrollTemplate extends Template
{

	/** @var InfiniteScrollComponent<T> */
	public InfiniteScrollComponent $control;

	public ?string $nextLink;

	public ?string $nextLinkAjax;

	public string $caption;

	public ?string $class;

	public ?string $linkClass;

	public ?int $offset;

}
