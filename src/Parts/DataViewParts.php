<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Parts;

use OutOfBoundsException;

final class DataViewParts
{

	/** @var array<string, object> */
	private array $parts;

	public function add(string $name, object $part): self
	{
		$this->parts[$name] = $part;

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
