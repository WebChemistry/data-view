<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Utility;

use Nette\Utils\Paginator;

final class PaginatorFactory
{

	public static function createPaginator(int $itemsPerPage, int $itemCount, int $page): Paginator
	{
		$paginator = new Paginator();

		$paginator->setPage($page);
		$paginator->setItemsPerPage($itemsPerPage);
		$paginator->setItemCount($itemCount);

		return $paginator;
	}

}
