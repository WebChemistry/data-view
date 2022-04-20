<?php declare(strict_types = 1);

namespace WebChemistry\DataView;

use Iterator;
use LogicException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Renderable;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Arrays;
use WebChemistry\DataView\DataSource\DataSet;
use WebChemistry\DataView\DataSource\DataSource;
use WebChemistry\DataView\Render\RenderCollection;
use WebChemistry\DataView\Template\DataViewComponentTemplate;

/**
 * @template T
 */
final class DataViewComponent extends Control
{

	/** @var array<array-key, callable(Control, DataViewComponent<T>): void> */
	public array $onRedrawRequest = [];

	/**
	 * @param DataSource<T> $dataSource
	 */
	public function __construct(private DataSource $dataSource)
	{
	}

	/**
	 * @return array<array-key, T>
	 */
	public function getData(): array
	{
		return $this->getDataSet()->getData();
	}

	/**
	 * @return DataSet<T>
	 */
	public function getDataSet(): DataSet
	{
		return $this->dataSource->getDataSet($this);
	}

	/**
	 * @template C of object
	 * @param class-string<C> $className
	 * @return C
	 */
	public function getComponentByNameAndClass(string $name, string $className): object
	{
		$object = $this->getComponent($name);
		if (!$object instanceof $className) {
			throw new LogicException(sprintf('Component %s is not instance of %s, %s given.', $name, $className, get_debug_type($object)));
		}

		return $object;
	}

	/**
	 * @param string[] $components
	 */
	public function render(array $components = []): void
	{
		/** @var DataViewComponentTemplate $template */
		$template = $this->createTemplate(DataViewComponentTemplate::class);
		$template->setFile(__DIR__ . '/templates/dataView.latte');
		$template->components = $this->getComponentsForRendering($components);

		$template->render();
	}

	public function requestRedraw(Control $control): void
	{
		Arrays::invoke($this->onRedrawRequest, $control, $this);
	}

	/**
	 * @param string[] $components
	 * @return iterable<int, Renderable>
	 */
	private function getComponentsForRendering(array $components): iterable
	{
		if (!$components) {
			/** @var Iterator<int, Renderable> $components */
			$components = $this->getComponents(filterType: Renderable::class);

			yield from (new RenderCollection($components))->getSortedCollectors();
		} else {
			foreach ($components as $name) {
				/** @var IComponent $component */
				$component = $this->getComponent($name);
				if (!$component instanceof Renderable) {
					throw new LogicException(sprintf('Component %s (%s) is not renderable.', $name, $component::class));
				}

				yield $component;
			}
		}
	}

}
