<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Paginator;

use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use WebChemistry\DataView\Component\Exception\PaginationOutOfBoundsException;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 */
abstract class BasePaginator extends Control implements Paginator
{

	private bool $strictOutOfBounds = true;

	/** @var list<callable(): void> */
	public array $onOutOfBounds = [];

	public function setStrictOutOfBounds(bool $strictOutOfBounds = true): static
	{
		$this->strictOutOfBounds = $strictOutOfBounds;

		return $this;
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

	/**
	 * @return DataViewComponent<T>
	 */
	protected function getDataView(): DataViewComponent
	{
		/** @var DataViewComponent<T> */
		return $this->lookup(DataViewComponent::class);
	}

	public function handlePaginate(): void
	{
		/** @var Presenter $presenter */
		$presenter = $this->getPresenter();

		if ($presenter->isAjax()) {
			$this->getDataView()->requestRedraw($this);
		}
	}

}
