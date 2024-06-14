<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Factory;

use WebChemistry\DataView\Component\PaginatorComponent;

/**
 * @template T
 */
interface PaginatorComponentFactory
{

	/**
	 * @return PaginatorComponent<T>
	 */
	public function create(): PaginatorComponent;

}
