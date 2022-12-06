<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Template;

use Nette\Bridges\ApplicationLatte\Template;
use WebChemistry\DataView\Component\FormComponent;

/**
 * @template T
 */
final class FormComponentTemplate extends Template
{

	/** @var FormComponent<T> */
	public FormComponent $control;

}
