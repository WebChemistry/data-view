<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet;

use InvalidArgumentException;
use WebChemistry\DataView\Cursor\Cursor;
use WebChemistry\DataView\Cursor\LimitCursor;
use WebChemistry\DataView\Cursor\OffsetCursor;

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
	public function getIterable(?Cursor $cursor = null): array
	{
		return $this->getData($cursor);
	}

	/**
	 * @return array<array-key, TValue>
	 */
	public function getData(?Cursor $cursor = null): array
	{
		$limit = $cursor?->getLimit();
		$offset = 0;

		if ($cursor instanceof OffsetCursor) {
			$offset = $cursor->getOffset();

		} else if (!$cursor instanceof LimitCursor && $cursor !== null) {
			throw new InvalidArgumentException(
				sprintf('Cursor must be instance of %s or %s', OffsetCursor::class, LimitCursor::class)
			);

		}

		return array_slice($this->data, $offset, $limit);
	}

	public function getCount(): int
	{
		return $this->count;
	}

}
