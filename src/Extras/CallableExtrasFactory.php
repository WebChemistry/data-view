<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Extras;

/**
 * @template TItem
 * @template TValue
 * @implements ExtrasFactory<TValue>
 */
final class CallableExtrasFactory implements ExtrasFactory
{

	/** @var callable(TItem[] $items): (callable(TItem $item): TValue) */
	private $factory;

	/**
	 * @param callable(TItem[] $items): (callable(TItem $item): TValue) $factory
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
		return new CallableExtras(($this->factory)($items));
	}

}
