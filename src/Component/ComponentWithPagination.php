<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\Attributes\Persistent;
use Nette\Utils\Paginator;
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

	/**
	 * @param int<1, max> $itemsPerPage
	 */
	public function __construct(
		protected int $itemsPerPage,
	)
	{
		$this->stepper = new PaginatorStepper();

		$this->onAnchor[] = function (): void {
			/** @var int<0, max> $offset */
			$offset = $this->getPaginator()->getOffset();

			$this->offset = $offset;
		};
	}

	/**
	 * @return int<0, max>|null
	 */
	public function getOffset(): ?int
	{
		return $this->offset;
	}

	/**
	 * @return int<0, max>
	 */
	public function getLimit(): int
	{
		return $this->itemsPerPage;
	}

	protected function getPaginator(): Paginator
	{
		if (!isset($this->paginator)) {
			$dataView = $this->getDataView();
			$dataSource = $dataView->getDataSource();
			$count = $dataSource->getDataSet($dataView)->getCount();

			if ($dataSource instanceof CacheableDataSource) {
				$dataSource->refresh();
			}

			$this->paginator = PaginatorFactory::createPaginator($this->itemsPerPage, $count, $this->page);
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

}
