<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Extras;

/**
 * @template TItem
 * @template TValue
 * @implements Extras<TItem, TValue>
 */
final class CallableExtras implements Extras
{

	/** @var callable(TItem $item): TValue */
	private $getter;

	/**
	 * @param callable(TItem $item): TValue $getter
	 */
	public function __construct(callable $getter)
	{
		$this->getter = $getter;
	}

	/**
	 * @param TItem $item
	 * @return TValue
	 */
	public function for(mixed $item): mixed
	{
		return ($this->getter)($item);
	}

}
