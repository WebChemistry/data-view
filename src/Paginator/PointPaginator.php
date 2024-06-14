<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Paginator;

use Nette\Application\Attributes\Persistent;
use WebChemistry\DataView\Cursor\Cursor;
use WebChemistry\DataView\Cursor\PointCursor;

/**
 * @template T
 * @extends BasePaginator<T>
 */
final class PointPaginator extends BasePaginator
{

	#[Persistent]
	public ?string $point = null;

	/** @var callable(T $item): (string|int|null) */
	private $getPointer;

	/**
	 * @param int<0, max> $itemsPerPage
	 * @param callable(T $item): (string|int|null) $getPointer
	 */
	public function __construct(
		private int $itemsPerPage,
		callable $getPointer,
	)
	{
		$this->getPointer = $getPointer;
	}

	public function createCurrentCursor(): Cursor
	{
		return new PointCursor($this->itemsPerPage, $this->point);
	}

	public function isFirstPage(): bool
	{
		return $this->point === null;
	}

	public function isLastPage(): bool
	{
		return count($this->getDataView()->getData()) < $this->itemsPerPage;
	}

	public function getNextLink(bool $ajax = false): ?string
	{
		if ($this->isLastPage()) {
			return null;
		}

		$data = $this->getDataView()->getData();
		$lastKey = array_key_last($data);

		if ($lastKey === null) {
			return null;
		}

		$point = ($this->getPointer)($data[$lastKey]);

		if ($point === null) {
			return null;
		}

		if (!$ajax) {
			return $this->link('this', ['point' => $point]);
		}

		return $this->link('paginate!', ['point' => $point]);
	}

	/**
	 * @param mixed[] $params
	 */
	public function loadState(array $params): void
	{
		parent::loadState($params);

		if ($this->point !== null && count($this->getDataView()->getData()) === 0) {
			$this->outOfBoundsDetected();
		}
	}

	public function getPrevLink(bool $ajax = false): ?string
	{
		return null;
	}

}
