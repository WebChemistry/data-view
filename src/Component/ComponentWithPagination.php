<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\Attributes\Persistent;
use Nette\Utils\Paginator;
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

	/** @var int[] */
	private array $steps;

	private PaginatorStepper $stepper;

	private ?int $offset = null;

	public function __construct(
		protected int $itemsPerPage,
	)
	{
		$this->stepper = new PaginatorStepper();

		$this->onAnchor[] = function (): void {
			$this->offset = $this->getPaginator()->getOffset();
		};
	}

	public function getOffset(): ?int
	{
		return $this->offset;
	}

	public function getLimit(): int
	{
		return $this->itemsPerPage;
	}

	protected function getPaginator(): Paginator
	{
		return $this->paginator ??= PaginatorFactory::createPaginator($this->itemsPerPage, $this->getDataView()->getDataSet()->getCount(), $this->page);
	}

	/**
	 * @return int[]
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
