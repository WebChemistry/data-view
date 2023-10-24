<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Extras;

use Generator;
use WebChemistry\DataView\DataSet\DataSet;

/**
 * @template TItem
 * @template TValue
 * @implements ExtrasFactory<TValue>
 */
final class YieldExtrasFactory implements ExtrasFactory
{

	/** @var callable(TItem[]): Generator<array-key, TValue, TItem, void> */
	private $factory;

	/**
	 * @param callable(TItem[]): Generator<array-key, TValue, TItem, void> $factory
	 */
	public function __construct(callable $factory)
	{
		$this->factory = $factory;
	}

	/**
	 * @param TItem[] $items
	 * @return Extras<TItem, TValue>
	 */
	public function create(array $items): Extras
	{
		return new YieldExtras(($this->factory)($items));
	}

}
