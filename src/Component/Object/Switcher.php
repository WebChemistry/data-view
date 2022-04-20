<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Object;

use InvalidArgumentException;

final class Switcher
{

	private string $link;

	private bool $value;

	/**
	 * @param mixed[] $options
	 */
	public function __construct(
		private string $id,
		private string $caption,
		private bool $default = false,
		private array $options = [],
	)
	{
		if (!preg_match('#^[\w.]+$#', $this->id)) {
			throw new InvalidArgumentException(
				sprintf('Name of switcher (%s) does not match regular expression.', $this->id)
			);
		}
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getCaption(): string
	{
		return $this->caption;
	}

	/**
	 * @return mixed[]
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	public function getDefault(): bool
	{
		return $this->default;
	}

	public function getValue(): bool
	{
		return $this->value;
	}

	public function getLink(): string
	{
		return $this->link;
	}

	public function isChanged(): bool
	{
		return $this->default !== $this->value;
	}

	/**
	 * @internal
	 */
	public static function injectLink(Switcher $switcher, string $link): void
	{
		$switcher->link = $link;
	}

	/**
	 * @internal
	 */
	public static function injectValue(Switcher $switcher, bool $value): void
	{
		$switcher->value = $value;
	}

}
