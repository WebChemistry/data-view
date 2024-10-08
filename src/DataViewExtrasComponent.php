<?php declare(strict_types = 1);

namespace WebChemistry\DataView;

use WebChemistry\DataView\DataSource\DataSource;
use WebChemistry\DataView\Extras\Extras;
use WebChemistry\DataView\Extras\ExtrasFactory;
use WebChemistry\DataView\Paginator\Paginator;

/**
 * @template TItem
 * @template TExtras
 * @extends DataViewComponent<TItem>
 */
final class DataViewExtrasComponent extends DataViewComponent
{

	/** @var Extras<TItem, TExtras> */
	private Extras $extras;

	/**
	 * @param DataSource<TItem> $dataSource
	 * @param ExtrasFactory<TExtras> $extrasFactory
	 */
	public function __construct(
		DataSource $dataSource,
		private ExtrasFactory $extrasFactory,
		?Paginator $paginator = null,
	)
	{
		parent::__construct($dataSource, $paginator);
	}

	/**
	 * @param TItem $item
	 * @return TExtras
	 */
	public function getExtrasFor(mixed $item): mixed
	{
		return ($this->extras ??= $this->extrasFactory->create($this->getData()))
			->for($item);
	}

}
