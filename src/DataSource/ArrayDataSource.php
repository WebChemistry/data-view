<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

use WebChemistry\DataView\DataSet\ArrayDataSet;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 * @implements DataSource<T>
 */
final class ArrayDataSource implements DataSource
{

	/**
	 * @param T[] $array
	 */
	public function __construct(
		private array $array,
	)
	{
	}

	/**
	 * @param DataViewComponent<T> $component
	 * @return ArrayDataSet<T>
	 */
	public function getDataSet(DataViewComponent $component): ArrayDataSet
	{
		return new ArrayDataSet(count($this->array), $this->array);
	}

}
