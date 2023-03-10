<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Extras;

use Generator;

/**
 * @template TItem
 * @template TValue
 * @implements Extras<TItem, TValue>
 */
final class YieldExtras implements Extras
{

	/**
	 * @param Generator<array-key, TValue, TItem, void> $generator
	 */
	public function __construct(
		private Generator $generator,
	)
	{
	}

	/**
	 * @param TItem $item
	 * @return TValue
	 */
	public function for(mixed $item): mixed
	{
		return $this->generator->send($item);
	}

}
