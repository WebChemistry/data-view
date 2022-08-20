<?php declare(strict_types = 1);

namespace WebChemistry\DataView;

use DomainException;
use Iterator;
use LogicException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Renderable;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Arrays;
use Symfony\Component\EventDispatcher\EventDispatcher;
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

	private EventDispatcher $eventDispatcher;

	/**
	 * @param DataSource<T> $dataSource
	 */
	public function __construct(private DataSource $dataSource)
	{
		if (class_exists(EventDispatcher::class)) {
			$this->eventDispatcher = new EventDispatcher();
		}
	}

	public function getEventDispatcher(): EventDispatcher
	{
		if (!isset($this->eventDispatcher)) {
			throw new LogicException(sprintf('Class %s does not exist.', EventDispatcher::class));
		}

		return $this->eventDispatcher;
	}

	/**
	 * @return DataSource<T>
	 */
	public function getDataSource(): DataSource
	{
		return $this->dataSource;
	}

	public function hasData(): bool
	{
		return $this->getDataSet()->hasData();
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
	public function getComponentByClass(string $className): object
	{
		$objects = iterator_to_array($this->getComponents(false, $className));
		$count = count($objects);

		if ($count === 1) {
			/** @var C */
			return Arrays::first($objects);
		}

		if ($count === 0) {
			throw new DomainException(
				sprintf('Missing component of type "%s" in data-view "%s".', $className, $this->getName())
			);
		}

		throw new DomainException(
			sprintf(
				'Multiple components of type "%s" in data-view "%s", components: %s.',
				$className,
				$this->getName(),
				implode(', ', array_map('get_class', $objects))
			)
		);
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

	public function renderCount(): void
	{
		echo $this->getDataSet()->getCount();
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
