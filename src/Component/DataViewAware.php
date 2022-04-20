<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 */
trait DataViewAware
{

	public function hasDataView(): bool
	{
		return (bool) $this->lookup(DataViewComponent::class, false);
	}

	/**
	 * @return DataViewComponent<T>
	 */
	public function getDataView(): DataViewComponent
	{
		/** @var DataViewComponent<T> $view */
		$view = $this->lookup(DataViewComponent::class);

		return $view;
	}

}
