<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet;

/**
 * @template TValue
 * @implements DataSet<TValue>
 */
final class ArrayDataSet implements DataSet
{

	/**
	 * @param array<array-key, TValue> $data
	 */
	public function __construct(
		private int $count,
		private array $data,
	)
	{
	}

	/**
	 * @return array<array-key, TValue>
	 */
	public function getIterable(): array
	{
		return $this->data;
	}

	/**
	 * @return array<array-key, TValue>
	 */
	public function getData(): array
	{
		return $this->data;
	}

	public function getCount(): int
	{
		return $this->count;
	}

	public function hasData(): bool
	{
		return $this->count > 0;
	}

}
