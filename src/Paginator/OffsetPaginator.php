<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Paginator;

use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Presenter;
use Nette\Utils\Paginator;
use WebChemistry\DataView\Cursor\Cursor;
use WebChemistry\DataView\Cursor\LimitCursor;
use WebChemistry\DataView\Cursor\OffsetCursor;
use WebChemistry\DataView\Utility\PaginatorFactory;

/**
 * @template T
 * @extends BasePaginator<T>
 */
final class OffsetPaginator extends BasePaginator
{

	#[Persistent]
	public int $page = 1;

	/** @var int<0, max>|null */
	private ?int $offset = null;

	private bool $computing = false;

	private Paginator $paginator;

	/**
	 * @param int<0, max> $itemsPerPage
	 */
	public function __construct(
		private int $itemsPerPage,
	)
	{
	}

	public function createCurrentCursor(): Cursor
	{
		$offset = $this->getOffset();

		if ($offset === null) {
			return new LimitCursor($this->getLimit());
		}

		return new OffsetCursor($offset, $this->getLimit());
	}

	/**
	 * @return int<0, max>|null
	 */
	public function getOffset(): ?int
	{
		if ($this->offset !== null) {
			return $this->offset;
		}

		if ($this->computing) {
			return null;
		}

		$this->computing = true;

		/** @var int<0, max> $offset */
		$offset = $this->getPaginator()->getOffset();

		$this->offset = $offset;

		$this->computing = false;

		return $this->offset;
	}

	/**
	 * @return int<0, max>
	 */
	public function getLimit(): int
	{
		return $this->itemsPerPage;
	}

	public function isFirstPage(): bool
	{
		return $this->getPaginator()->isFirst();
	}

	public function isLastPage(): bool
	{
		return $this->getPaginator()->isLast();
	}

	public function getPageCount(): int
	{
		return $this->getPaginator()->getPageCount() ?? 1;
	}

	public function getNextLink(bool $ajax = false): ?string
	{
		if ($this->page >= $this->getPaginator()->getPageCount()) {
			return null;
		}

		if (!$ajax) {
			return $this->link('this', ['page' => $this->page + 1]);
		}

		return $this->link('paginate!', ['page' => $this->page + 1]);
	}

	/**
	 * @internal
	 */
	public function getLink(int $page, bool $ajax = false): string
	{
		return $this->link($ajax ? 'paginate!' : 'this', ['page' => $page]);
	}

	public function getPrevLink(bool $ajax = false): ?string
	{
		if ($this->page <= 1) {
			return null;
		}

		if (!$ajax) {
			return $this->link('this', ['page' => $this->page - 1]);
		}

		return $this->link('paginate!', ['page' => $this->page - 1]);
	}

	private function getPaginator(): Paginator
	{
		if (!isset($this->paginator)) {
			$dataView = $this->getDataView();
			$count = $dataView->count();

			$paginator = PaginatorFactory::createPaginator($this->itemsPerPage, $count, $this->page);
			$pageCount = $paginator->getPageCount();

			if ($this->page < 1) {
				$this->outOfBoundsDetected();
			} else if ($this->page !== 1 && $this->page > $pageCount) {
				$this->outOfBoundsDetected();
			}

			$this->paginator = $paginator;
		}

		return $this->paginator;
	}

	/**
	 * @return int<1, max>[]
	 */
	public function getSteps(): array
	{
		return (new PaginatorStepper())->getSteps($this->getPaginator());
	}

}
