<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Factory;

use WebChemistry\DataView\Component\InfiniteScrollComponent;

/**
 * @template T
 */
interface InfiniteScrollComponentFactory
{

	/**
	 * @return InfiniteScrollComponent<T>
	 */
	public function create(): InfiniteScrollComponent;

}
