<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet;

use WebChemistry\DataView\Cursor\Cursor;
use WebChemistry\DataView\DataSet\Cache\DataSetCache;

/**
 * @template TValue
 * @implements DataSet<TValue>
 */
final class CacheableDataSet implements DataSet
{

	private int $count;

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
		private ?DataSetCache $dataSetCache = null,
	)
	{
	}

	/**
	 * @return iterable<TValue>
	 */
	public function getIterable(?Cursor $cursor = null): iterable
	{
		if (isset($this->iterable[$cursor?->getUid()])) {
			return $this->iterable[$cursor?->getUid()];
		}

		$item = $this->dataSetCache?->get('data', fn () => $this->dataSet->getData($cursor), $cursor);

		if ($item !== null) {
			return $this->iterable[$cursor?->getUid()] ??= $item;
		}

		if (!$this->cacheIterable) {
			return $this->dataSet->getIterable($cursor);
		}

		return $this->iterable[$cursor?->getUid()] ??= $this->dataSet->getIterable($cursor);
	}

	/**
	 * @return array<TValue>
	 */
	public function getData(?Cursor $cursor = null): array
	{
		if (isset($this->data[$cursor?->getUid()])) {
			return $this->data[$cursor?->getUid()];
		}

		$item = $this->dataSetCache?->get('data', fn () => $this->dataSet->getData($cursor), $cursor);

		if ($item !== null) {
			return $this->data[$cursor?->getUid()] ??= $item;
		}

		return $this->data[$cursor?->getUid()] ??= $this->dataSet->getData($cursor);
	}

	public function getCount(): int
	{
		if (isset($this->count)) {
			return $this->count;
		}

		$item = $this->dataSetCache?->get('count', fn () => $this->dataSet->getCount(), null);

		if ($item !== null) {
			return $this->count = $item;
		}

		return $this->count = $this->dataSet->getCount();
	}

	public function hasData(): bool
	{
		return $this->getCount() > 0;
	}

}
