<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Paginator;

use WebChemistry\DataView\Cursor\Cursor;
use WebChemistry\DataView\Cursor\LimitCursor;

/**
 * @template T
 * @extends BasePaginator<T>
 */
final class FullPaginator extends BasePaginator
{

	public function createCurrentCursor(): Cursor
	{
		return new LimitCursor(null);
	}

	public function isFirstPage(): bool
	{
		return true;
	}

	public function isLastPage(): bool
	{
		return true;
	}

	public function getNextLink(bool $ajax = false): ?string
	{
		return null;
	}

	public function getPrevLink(bool $ajax = false): ?string
	{
		return null;
	}

}
