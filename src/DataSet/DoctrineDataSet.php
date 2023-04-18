<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet;

use ArrayIterator;
use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;
use WebChemistry\DataView\Cursor\Cursor;
use WebChemistry\DataView\Cursor\LimitCursor;
use WebChemistry\DataView\Cursor\OffsetCursor;

/**
 * @template TValue
 * @implements DataSet<TValue>
 */
final class DoctrineDataSet implements DataSet
{

	/**
	 * @param Paginator<TValue> $paginator
	 */
	public function __construct(
		private Paginator $paginator,
	)
	{
	}

	/**
	 * @return ArrayIterator<array-key, TValue>
	 */
	public function getIterable(?Cursor $cursor = null): ArrayIterator
	{
		$limit = $cursor?->getLimit();
		$offset = null;

		if ($cursor instanceof OffsetCursor) {
			$offset = $cursor->getOffset();

		} else if (!$cursor instanceof LimitCursor && $cursor !== null) {
			throw new InvalidArgumentException(
				sprintf('Cursor must be instance of %s or %s', OffsetCursor::class, LimitCursor::class)
			);

		}

		$this->paginator->getQuery()
			->setFirstResult($offset)
			->setMaxResults($limit);

		return $this->paginator->getIterator();
	}

	/**
	 * @return array<array-key, TValue>
	 */
	public function getData(?Cursor $cursor = null): array
	{
		return $this->getIterable($cursor)->getArrayCopy();
	}

	public function getCount(): int
	{
		return $this->paginator->count();
	}

	public function hasData(): bool
	{
		return $this->paginator->count() > 0;
	}

}
