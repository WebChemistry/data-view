<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Template;

use Nette\Bridges\ApplicationLatte\Template;
use WebChemistry\DataView\Component\SnippetComponent;

final class SnippetComponentTemplate extends Template
{

	public SnippetComponent $control;

	public string $name;

}
