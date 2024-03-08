<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

use WebChemistry\DataView\DataSet\DataSet;
use WebChemistry\DataView\DataSet\DecorateDataSet;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template TValue
 * @template TId
 * @implements DataSource<TValue>
 */
final class DecorateDataSource implements DataSource
{

	/** @var callable(TId[] $ids, DataViewComponent<TValue> $component): TValue[] */
	private $callback;

	/**
	 * @param DataSource<TId> $dataSource
	 * @param callable(TId[] $ids, DataViewComponent<TValue> $component): TValue[] $callback
	 */
	public function __construct(
		private DataSource $dataSource,
		callable $callback,
	)
	{
		$this->callback = $callback;
	}

	/**
	 * @param DataViewComponent<TValue> $component
	 * @return DataSet<TValue>
	 */
	public function getDataSet(DataViewComponent $component): DataSet
	{
		return new DecorateDataSet(
			$this->dataSource->getDataSet($component), // @phpstan-ignore-line
			function (callable $get) use ($component): array {
				return ($this->callback)($get(), $component);
			},
			fn (callable $get) => $get(),
		);
	}

}
