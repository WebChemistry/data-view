<?php declare(strict_types = 1);

namespace WebChemistry\DataView;

use ArrayIterator;
use Countable;
use DomainException;
use Exception;
use Iterator;
use IteratorAggregate;
use LogicException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Renderable;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Arrays;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Traversable;
use WebChemistry\DataView\Component\ComponentWithPagination;
use WebChemistry\DataView\Cursor\Cursor;
use WebChemistry\DataView\DataSet\DataSet;
use WebChemistry\DataView\DataSource\DataSource;
use WebChemistry\DataView\Parts\DataViewParts;
use WebChemistry\DataView\Render\RenderCollection;
use WebChemistry\DataView\Template\DataViewComponentTemplate;

/**
 * @template T
 * @implements IteratorAggregate<array-key, T>
 */
class DataViewComponent extends Control implements IteratorAggregate, Countable
{

	/** @var array<array-key, callable(Control, DataViewComponent<T>): void> */
	public array $onRedrawRequest = [];

	/** @var array<array-key, callable(DataViewComponent<T>, DataViewComponentTemplate): void> */
	public array $onRender = [];

	protected EventDispatcher $eventDispatcher;

	/** @var array<T> */
	private array $data;

	/** @var DataViewParts<T> */
	private DataViewParts $parts;

	/**
	 * @param DataSource<T> $dataSource
	 */
	public function __construct(
		protected DataSource $dataSource,
	)
	{
		if (class_exists(EventDispatcher::class)) {
			$this->eventDispatcher = new EventDispatcher();
		}

		$this->parts = new DataViewParts($this);
	}

	public function getEventDispatcher(): EventDispatcher
	{
		if (!isset($this->eventDispatcher)) {
			throw new LogicException(sprintf('Class %s does not exist.', EventDispatcher::class));
		}

		return $this->eventDispatcher;
	}

	/**
	 * @return DataViewParts<T>
	 */
	public function getParts(): DataViewParts
	{
		return $this->parts;
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
		return $this->data ??= $this->getDataSet()->getData($this->createCursor());
	}

	public function count(): int
	{
		return $this->getDataSet()->getCount();
	}

	/**
	 * @return iterable<array-key, T>
	 */
	public function getIterableData(): iterable
	{
		return $this->getDataSet()->getIterable($this->createCursor());
	}

	private function createCursor(): ?Cursor
	{
		return $this->getOptionalComponentByClass(ComponentWithPagination::class)?->createCursor();
	}

	/**
	 * @return ArrayIterator<array-key, T>
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->getData());
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
		$component = $this->getOptionalComponentByClass($className);

		if (!$component) {
			throw new DomainException(
				sprintf('Missing component of type "%s" in data-view "%s".', $className, $this->getName())
			);
		}

		return $component;
	}

	/**
	 * @template C of object
	 * @param class-string<C> $className
	 * @return C|null
	 */
	public function getOptionalComponentByClass(string $className): ?object
	{
		$objects = iterator_to_array($this->getComponents(false, $className));
		$count = count($objects);

		if ($count === 1) {
			/** @var C */
			return Arrays::first($objects);
		}

		if ($count === 0) {
			return null;
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
	 * @return ComponentWithPagination<T>
	 */
	public function getPaginationComponent(): ComponentWithPagination
	{
		return $this->getComponentByClass(ComponentWithPagination::class);
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

		foreach ($this->onRender as $callback) {
			$callback($this, $template);
		}

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
