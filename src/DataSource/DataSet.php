<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

/**
 * @template T
 */
class DataSet
{

	/**
	 * @param iterable<array-key, T> $data
	 */
	public function __construct(
		private int $count,
		private iterable $data,
	)
	{
	}

	/**
	 * @return iterable<array-key, T>
	 */
	public function getIterable(): iterable
	{
		return $this->data;
	}

	/**
	 * @return array<array-key, T>
	 */
	public function getData(): array
	{
		if (is_array($this->data)) {
			return $this->data;
		}

 		return iterator_to_array($this->data);
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
