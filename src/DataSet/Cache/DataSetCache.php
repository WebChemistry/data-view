<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet\Cache;

use WebChemistry\DataView\Cursor\Cursor;

interface DataSetCache
{

	/**
	 * @template TValue
	 * @param callable(): TValue $callback
	 * @return TValue|null
	 */
	public function get(string $key, callable $callback, ?Cursor $cursor): mixed;

}
