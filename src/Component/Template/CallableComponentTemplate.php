<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Template;

use Nette\Bridges\ApplicationLatte\Template;
use WebChemistry\DataView\Component\CallableComponent;

/**
 * @template T
 */
final class CallableComponentTemplate extends Template
{

	/** @var CallableComponent<T> */
	public CallableComponent $control;

	public string $name;

}
