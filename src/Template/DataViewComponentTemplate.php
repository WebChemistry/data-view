<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Template;

use Nette\Application\UI\Renderable;
use Nette\Bridges\ApplicationLatte\Template;

final class DataViewComponentTemplate extends Template
{

	/** @var iterable<array-key, Renderable> */
	public iterable $components;

}
