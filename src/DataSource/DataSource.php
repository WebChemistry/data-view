<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

use WebChemistry\DataView\DataSet\DataSet;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 */
interface DataSource
{

	/**
	 * @param DataViewComponent<T> $component
	 * @return DataSet<T>
	 */
	public function getDataSet(DataViewComponent $component): DataSet;

}
