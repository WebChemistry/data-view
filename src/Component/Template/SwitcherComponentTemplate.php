<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Template;

use Nette\Bridges\ApplicationLatte\Template;
use WebChemistry\DataView\Component\Object\Switcher;
use WebChemistry\DataView\Component\SwitcherComponent;

final class SwitcherComponentTemplate extends Template
{

	public SwitcherComponent $control;

	/** @var Switcher[] */
	public array $switchers;

	public bool $ajax;

}
