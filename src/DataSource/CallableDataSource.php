<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

use LogicException;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 * @implements DataSource<T>
 */
final class CallableDataSource implements DataSource
{

	/** @var callable(DataViewComponent<T>): DataSource<T> */
	private $callable;

	/**
	 * @param callable(DataViewComponent<T>): DataSource<T> $callable
	 */
	public function __construct(callable $callable)
	{
		$this->callable = $callable;
	}

	/**
	 * @param DataViewComponent<T> $component
	 */
	public function getDataSet(DataViewComponent $component): DataSet
	{
		$source = ($this->callable)($component);

		if (!$source instanceof DataSource) {
			throw new LogicException(
				sprintf(
					'Callback of CallableDataSource must returns %s, %s returned.',
					DataSource::class,
					get_debug_type($source)
				)
			);
		}

		return $source->getDataSet($component);
	}

}
