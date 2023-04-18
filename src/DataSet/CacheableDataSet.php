<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet;

use WebChemistry\DataView\Cursor\Cursor;

/**
 * @template TValue
 * @implements DataSet<TValue>
 */
final class CacheableDataSet implements DataSet
{

	private int $count;

	private bool $hasData;

	/** @var array<string, array<TValue>> */
	private array $data = [];

	/** @var array<string, iterable<TValue>> */
	private array $iterable = [];

	/**
	 * @param DataSet<TValue> $dataSet
	 */
	public function __construct(
		private DataSet $dataSet,
		private bool $cacheIterable = false,
	)
	{
	}

	/**
	 * @return iterable<TValue>
	 */
	public function getIterable(?Cursor $cursor = null): iterable
	{
		if ($this->cacheIterable) {
			return $this->iterable[$cursor?->getUid()] ??= $this->dataSet->getIterable($cursor);
		}

		return $this->dataSet->getIterable($cursor);
	}

	/**
	 * @return array<TValue>
	 */
	public function getData(?Cursor $cursor = null): array
	{
		return $this->data[$cursor?->getUid()] ??= $this->dataSet->getData($cursor);
	}

	public function getCount(): int
	{
		return $this->count ??= $this->dataSet->getCount();
	}

	public function hasData(): bool
	{
		return $this->hasData ??= $this->dataSet->hasData();
	}

}
