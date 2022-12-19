<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\Attributes\Persistent;
use Nette\Utils\Paginator;
use WebChemistry\DataView\Component\Exception\PaginationOutOfBoundsException;
use WebChemistry\DataView\DataSource\CacheableDataSource;
use WebChemistry\DataView\Paginator\PaginatorStepper;
use WebChemistry\DataView\Utility\PaginatorFactory;

/**
 * @template T
 * @extends BaseViewComponent<T>
 */
abstract class ComponentWithPagination extends BaseViewComponent
{

	#[Persistent]
	public int $page = 1;

	private Paginator $paginator;

	/** @var int<1, max>[] */
	private array $steps;

	private PaginatorStepper $stepper;

	/** @var int<0, max>|null */
	private ?int $offset = null;

	private bool $computing = false;

	private bool $strictOutOfBounds = false;

	/** @var list<callable(): void> */
	public array $onOutOfBounds = [];

	/**
	 * @param int<1, max> $itemsPerPage
	 */
	public function __construct(
		protected int $itemsPerPage,
	)
	{
		$this->stepper = new PaginatorStepper();
	}

	public function setStrictOutOfBounds(bool $strictOutOfBounds = true): static
	{
		$this->strictOutOfBounds = $strictOutOfBounds;

		return $this;
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

	public function getPaginator(): Paginator
	{
		if (!isset($this->paginator)) {
			$dataView = $this->getDataView();
			$dataSource = $dataView->getDataSource();
			$count = $dataSource->getDataSet($dataView)->getCount();

			if ($dataSource instanceof CacheableDataSource) {
				$dataSource->refresh();
			}

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
		return $this->steps ??= $this->stepper->getSteps($this->getPaginator());
	}

	public function getStepper(): PaginatorStepper
	{
		return $this->stepper;
	}

	protected function outOfBoundsDetected(): void
	{
		foreach ($this->onOutOfBounds as $callback) {
			$callback();
		}

		if ($this->strictOutOfBounds) {
			throw new PaginationOutOfBoundsException();
		}
	}

}
