<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet;

use WebChemistry\DataView\Cursor\Cursor;

/**
 * @template TValue
 */
interface DataSet
{

	/**
	 * @return array<array-key, TValue>
	 */
	public function getIterable(?Cursor $cursor = null): iterable;

	/**
	 * @return array<array-key, TValue>
	 */
	public function getData(?Cursor $cursor = null): array;

	public function getCount(): int;

}
