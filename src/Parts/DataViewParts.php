<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Parts;

use OutOfBoundsException;

final class DataViewParts
{

	/** @var array<string, object> */
	private array $parts;

	/** @var array<string, callable[]> */
	private array $monitors = [];

	/**
	 * @param callable(object): void $callback
	 */
	public function monitor(string $name, callable $callback): self
	{
		$this->monitors[$name][] = $callback;

		if (isset($this->parts[$name])) {
			$callback($this->parts[$name]);
		}

		return $this;
	}

	public function add(string $name, object $part): self
	{
		$this->parts[$name] = $part;

		if (isset($this->monitors[$name])) {
			foreach ($this->monitors[$name] as $callback) {
				$callback($part);
			}
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

	public function remove(string $name): self
	{
		unset($this->parts[$name]);

		return $this;
	}

}
