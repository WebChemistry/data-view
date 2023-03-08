<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Template;

use Nette\Bridges\ApplicationLatte\Template;
use WebChemistry\DataView\Component\LoadMoreComponent;

/**
 * @template T
 */
final class LoadMoreComponentTemplate extends Template
{

	/** @var LoadMoreComponent<T> */
	public LoadMoreComponent $control;

	public string $caption;

	public ?string $nextLink;

	public ?string $nextLinkAjax;

	public ?string $class;

	public ?string $linkClass;

}
