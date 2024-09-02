<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

use Doctrine\ORM\QueryBuilder;
use LogicException;
use WebChemistry\DataView\DataSet\DataSet;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 * @implements DataSource<T>
 */
final class DataSourceFacade implements DataSource
{

	/** @var callable(DataViewComponent<T>): mixed */
	private $dataSourceFactory;

	/** @var (callable(mixed[] $ids, DataViewComponent<T> $component): T[])|null */
	private $decorator;

	/** @var string|(callable(callable(string $contains): bool): bool)|null */
	private $fastSignal;

	/** @var DataSource<T> */
	private DataSource $dataSource;

	/**
	 * @param callable(DataViewComponent<T>): mixed $dataSourceFactory
	 * @param (callable(mixed[] $ids, DataViewComponent<T> $component): T[])|null $decorator
	 * @param string|(callable(callable(string $contains): bool): bool)|null $fastSignal
	 */
	public function __construct(
		callable $dataSourceFactory,
		?callable $decorator = null,
		callable|string|null $fastSignal = null,
		private bool $cacheable = true,
	)
	{
		$this->dataSourceFactory = $dataSourceFactory;
		$this->decorator = $decorator;
		$this->fastSignal = $fastSignal;
	}

	/**
	 * @param DataViewComponent<T> $component
	 * @return DataSet<T>
	 */
	public function getDataSet(DataViewComponent $component): DataSet
	{
		if (!isset($this->dataSource)) {
			$dataSource = ($this->dataSourceFactory)($component);

			if (!$dataSource instanceof DataSource) {
				if (is_array($dataSource)) {
					$dataSource = new ArrayDataSource($dataSource);
				} elseif ($dataSource instanceof QueryBuilder) {
					$dataSource = new DoctrineDataSource($dataSource);
				} else {
					throw new LogicException(
						sprintf(
							'Callback of %s must returns %s, %s returned.',
							self::class,
							implode('|', [DataSource::class, 'array', QueryBuilder::class]),
							get_debug_type($dataSource)
						)
					);
				}
			}

			if ($this->decorator) {
				$dataSource = new DecorateDataSource($dataSource, $this->decorator);
			}

			if ($this->cacheable) {
				$dataSource = new CacheableDataSource($dataSource);
			}

			if ($this->fastSignal) {
				$dataSource = new FastSignalDataSource($this->fastSignal, $dataSource);
			}

			$this->dataSource = $dataSource;
		}

		/** @var DataSet<T> */
		return $this->dataSource->getDataSet($component);
	}

}
