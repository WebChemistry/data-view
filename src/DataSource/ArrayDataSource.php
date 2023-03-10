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

	/** @var int<0, max> */
	private int $offset;

	/**
	 * @param T[] $array
	 * @param int<0, max>|null $limit
	 * @param int<0, max>|null $offset
	 */
	public function __construct(
		private array $array,
		private ?int $limit = null,
		?int $offset = null,
	)
	{
		$this->setOffset($offset);
	}

	/**
	 * @param int<0, max>|null $limit
	 */
	public function setLimit(?int $limit): void
	{
		if ($limit !== null) {
			$limit = max(0, $limit);
		}

		$this->limit = $limit;
	}

	/**
	 * @param int<0, max>|null $offset
	 */
	public function setOffset(?int $offset): void
	{
		if ($offset === null) {
			$offset = 0;
		}

		$this->offset = max(0, $offset);
	}

	/**
	 * @param DataViewComponent<T> $component
	 * @return ArrayDataSet<T>
	 */
	public function getDataSet(DataViewComponent $component): ArrayDataSet
	{
		return new ArrayDataSet(count($this->array), array_slice($this->array, $this->offset, $this->limit));
	}

}
