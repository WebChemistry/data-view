<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\CacheInterface;
use WebChemistry\DataView\DataSet\Cache\DataSetCache;
use WebChemistry\DataView\DataSet\CacheableDataSet;
use WebChemistry\DataView\DataSet\CacheDataSet;
use WebChemistry\DataView\DataSet\DataSet;
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
		private ?DataSetCache $dataSetCache = null,
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
		return $this->dataSet ??= new CacheableDataSet($this->dataSource->getDataSet($component), dataSetCache: $this->dataSetCache);
	}

}
