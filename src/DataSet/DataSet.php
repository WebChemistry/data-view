<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet;

/**
 * @template TValue
 */
interface DataSet
{

	/**
	 * @return array<array-key, TValue>
	 */
	public function getIterable(): iterable;

	/**
	 * @return array<array-key, TValue>
	 */
	public function getData(): array;

	public function getCount(): int;
	
	public function hasData(): bool;
	
}
