<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 * @implements DataSource<T>
 */
final class CacheableDataSource implements DataSource
{

	/** @var DataSet<T> */
	private DataSet $dataSet;

	/**
	 * @param DataSource<T> $dataSource
	 */
	public function __construct(
		private DataSource $dataSource,
	)
	{
	}

	public function refresh(): void
	{
		unset($this->dataSet);
	}

	/**
	 * @param DataViewComponent<T> $component
	 * @return DataSet<T>
	 */
	public function getDataSet(DataViewComponent $component): DataSet
	{
		if (!isset($this->dataSet)) {
			$this->dataSet = $this->dataSource->getDataSet($component);
		}

		return $this->dataSet;
	}

}
