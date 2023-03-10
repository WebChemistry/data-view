<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet;

use IteratorIterator;
use Traversable;

/**
 * @template TValue
 * @implements DataSet<TValue>
 */
final class IterableDataSet implements DataSet
{

	/** @var array<array-key, TValue> */
	private array $cache;

	/**
	 * @param iterable<array-key, TValue> $iterable
	 */
	public function __construct(
		private int $count,
		private iterable $iterable,
	)
	{
	}

	/**
	 * @return iterable<array-key, TValue>
	 */
	public function getIterable(): iterable
	{
		return $this->createIterable();
	}

	/**
	 * @return array<array-key, TValue>
	 */
	public function getData(): array
	{
		return $this->cache ??= iterator_to_array($this->createIterable()); // @phpstan-ignore-line
	}

	public function getCount(): int
	{
		return $this->count;
	}

	public function hasData(): bool
	{
		return $this->count > 0;
	}

	/**
	 * @return iterable<array-key, TValue>
	 */
	private function createIterable(): iterable
	{
		$iterable = $this->iterable;
		
		return $iterable instanceof Traversable ? new IteratorIterator($iterable) : $iterable; // @phpstan-ignore-line
	}

}
