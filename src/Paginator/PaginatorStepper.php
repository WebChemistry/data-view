<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Paginator;

use LogicException;
use Nette\Utils\Paginator;

final class PaginatorStepper
{

	public function __construct(
		private int $numberOfPrevious = 3,
		private int $numberOfNext = 2,
		private bool $showFirst = true,
		private bool $showLast = false,
		private ?int $distantValue = null,
	)
	{
	}

	public function setNumberOfPrevious(int $numberOfPrevious): static
	{
		$this->numberOfPrevious = $numberOfPrevious;

		return $this;
	}

	public function setNumberOfNext(int $numberOfNext): static
	{
		$this->numberOfNext = $numberOfNext;

		return $this;
	}

	public function setShowFirst(bool $showFirst): static
	{
		$this->showFirst = $showFirst;

		return $this;
	}

	public function setShowLast(bool $showLast): static
	{
		$this->showLast = $showLast;

		return $this;
	}

	public function setDistantValue(?int $distantValue): static
	{
		$this->distantValue = $distantValue;

		return $this;
	}

	/**
	 * @return int[]
	 */
	public function getSteps(Paginator $paginator): array
	{
		$lastPage = $paginator->getLastPage();
		if ($lastPage === null) {
			throw new LogicException('Cannot get last page, because item count is not set.');
		}

		$steps = range(
			max($paginator->getFirstPage(), $paginator->getPage() - $this->numberOfPrevious),
			min($lastPage, $paginator->getPage() + $this->numberOfNext),
		);

		if ($this->showFirst) {
			array_unshift($steps, $paginator->getFirstPage());
		}

		if ($this->distantValue !== null) {
			$page = min($lastPage, $paginator->getPage() + $this->distantValue);

			if ($lastPage - $page > 3) {
				$steps[] = $page;
			}
		}

		if ($this->showLast) {
			$steps[] = $lastPage;
		}

		return array_values(array_unique($steps));
	}

}
