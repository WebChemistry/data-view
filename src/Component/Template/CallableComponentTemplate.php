<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Template;

use Nette\Bridges\ApplicationLatte\Template;
use WebChemistry\DataView\Component\CallableComponent;

final class CallableComponentTemplate extends Template
{

	public CallableComponent $control;

	public string $name;

}
