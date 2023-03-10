<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Extras;

use WebChemistry\DataView\DataSet\DataSet;

/**
 * @template TValue
 */
interface ExtrasFactory
{

	/**
	 * @template TItem
	 * @param DataSet<TItem> $dataSet
	 * @return Extras<TItem, TValue>
	 */
	public function create(DataSet $dataSet): Extras;

}
