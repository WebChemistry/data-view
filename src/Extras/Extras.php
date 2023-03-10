<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Extras;

/**
 * @template TItem
 * @template TValue
 */
interface Extras
{

	/**
	 * @param TItem $item
	 * @return TValue
	 */
	public function for(mixed $item): mixed;

}
