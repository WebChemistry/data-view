<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\UI\Control;
use WebChemistry\DataView\Render\RenderPrioritization;
use WebChemistry\DataView\Render\RenderWithPriority;

/**
 * @template T
 */
abstract class BaseViewComponent extends Control implements RenderWithPriority
{

	/** @use DataViewAware<T> */
	use DataViewAware;
	use RenderPrioritization;

}
