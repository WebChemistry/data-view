<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

use OutOfBoundsException;
use WebChemistry\DataView\DataSet\ArrayDataSet;
use WebChemistry\DataView\DataSet\DataSet;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 * @implements DataSource<T>
 */
final class FastSignalDataSource implements DataSource
{

	/**
	 * @param DataSource<T> $dataSource
	 */
	public function __construct(
		private string $componentName,
		private DataSource $dataSource,
		private bool $strict = true,
	)
	{
	}

	/**
	 * @param DataViewComponent<T> $component
	 * @return DataSet<T>
	 */
	public function getDataSet(DataViewComponent $component): DataSet
	{
		if ($this->strict) {
			$presenter = $component->getPresenter();
		} else if (!($presenter = $component->getPresenterIfExists())) {
			return $this->dataSource->getDataSet($component);
		}

		if (!$component->getComponent($this->componentName, false)) {
			$component->onRender[] = function (DataViewComponent $component): void {
				if (!$component->getComponent($this->componentName, false)) {
					throw new OutOfBoundsException(
						sprintf('Component "%s" does not exist in data view required for fast signal.', $this->componentName)
					);
				}
			};
		}

		if ($presenter && $presenter->isAjax()) {
			$id = $component->getUniqueId() . $component::NAME_SEPARATOR . $this->componentName . $component::NAME_SEPARATOR;
			$signalName = $presenter->getSignal()[0] ?? null;

			if (is_string($signalName) && str_starts_with($signalName, $id)) {
				return new ArrayDataSet(0, []);
			}
		}

		return $this->dataSource->getDataSet($component);
	}

}
