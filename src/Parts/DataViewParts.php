<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Parts;

use OutOfBoundsException;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 */
final class DataViewParts
{

	/** @var array<string, object> */
	private array $parts;

	/** @var array<string, array<callable(object, DataViewComponent<T>=): void>> */
	private array $monitors = [];

	/**
	 * @param DataViewComponent<T> $dataViewComponent
	 */
	public function __construct(
		private DataViewComponent $dataViewComponent,
	)
	{
	}

	/**
	 * @param callable(object, DataViewComponent<T>=): void $callback
	 * @return self<T>
	 */
	public function monitor(string $name, callable $callback): self
	{
		$this->monitors[$name][] = $callback;

		if (isset($this->parts[$name])) {
			$callback($this->parts[$name], $this->dataViewComponent);
		}

		return $this;
	}

	/**
	 * @return self<T>
	 */
	public function add(string $name, object $part): self
	{
		$this->parts[$name] = $part;

		foreach ($this->monitors[$name] ?? [] as $callback) {
			$callback($part, $this->dataViewComponent);
		}

		return $this;
	}

	public function get(string $name): object
	{
		if (!isset($this->parts[$name])) {
			throw new OutOfBoundsException(sprintf('Part %s not found.', $name));
		}

		return $this->parts[$name];
	}

	public function tryGet(string $name): ?object
	{
		return $this->parts[$name] ?? null;
	}

	public function has(string $name): bool
	{
		return isset($this->parts[$name]);
	}

	/**
	 * @return self<T>
	 */
	public function remove(string $name): self
	{
		unset($this->parts[$name]);

		return $this;
	}

}
