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

	/** @var string|callable(callable(string $contains): bool, string $signal): bool */
	private $componentNameOrCallback;

	/**
	 * @param string|callable(callable(string $contains): bool): bool $componentNameOrCallback
	 * @param DataSource<T> $dataSource
	 */
	public function __construct(
		string|callable $componentNameOrCallback,
		private DataSource $dataSource,
		private bool $strict = true,
	)
	{
		$this->componentNameOrCallback = $componentNameOrCallback;
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

		$componentNameOrCallback = $this->componentNameOrCallback;

		if (is_string($componentNameOrCallback) && !$component->getComponent($componentNameOrCallback, false)) {
			$component->onRender[] = function (DataViewComponent $component) use ($componentNameOrCallback): void {
				if (!$component->getComponent($componentNameOrCallback, false)) {
					throw new OutOfBoundsException(
						sprintf('Component "%s" does not exist in data view required for fast signal.', $componentNameOrCallback)
					);
				}
			};
		}

		if ($presenter && $presenter->isAjax()) {
			$signalName = $presenter->getSignal()[0] ?? null;

			if (!is_string($signalName)) {
				return $this->dataSource->getDataSet($component);
			}

			$signalName = substr($signalName, strlen($component->getUniqueId() . $component::NAME_SEPARATOR));

			if (is_string($componentNameOrCallback)) {
				if (str_starts_with($signalName, $componentNameOrCallback . $component::NAME_SEPARATOR)) {
					return new ArrayDataSet(0, []);
				}

			} else {
				$components = explode($component::NAME_SEPARATOR, $signalName);
				$contains = fn (string $name) => in_array($name, $components, true);

				if (($componentNameOrCallback)($contains, $signalName)) {
					return new ArrayDataSet(0, []);
				}
			}
		}

		return $this->dataSource->getDataSet($component);
	}

}
