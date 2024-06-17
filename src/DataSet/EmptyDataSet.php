<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet;

use WebChemistry\DataView\Cursor\Cursor;

/**
 * @template T
 * @implements DataSet<T>
 */
final class EmptyDataSet implements DataSet
{

	public function getIterable(?Cursor $cursor = null): iterable
	{
		return [];
	}

	public function getData(?Cursor $cursor = null): array
	{
		return [];
	}

	public function getCount(): int
	{
		return 0;
	}

}
