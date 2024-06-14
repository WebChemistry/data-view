<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet;

use WebChemistry\DataView\Cursor\Cursor;

/**
 * @template TValue
 * @template TDecorate
 * @implements DataSet<TValue>
 */
final class DecorateDataSet implements DataSet
{

	/** @var callable(callable(): TDecorate[] $get): TValue[] */
	private $dataCallback;

	/** @var callable(callable(): int $get): int */
	private $countCallback;

	/**
	 * @param DataSet<TDecorate> $decorate
	 * @param callable(callable(): TDecorate[] $get): TValue[] $dataCallback
	 * @param callable(callable(): int $get): int $countCallback
	 */
	public function __construct(
		private DataSet $decorate,
		callable $dataCallback,
		callable $countCallback,
	)
	{
		$this->dataCallback = $dataCallback;
		$this->countCallback = $countCallback;
	}

	/**
	 * @return TValue[]
	 */
	public function getIterable(?Cursor $cursor = null): iterable
	{
		return $this->getData($cursor);
	}

	/**
	 * @return TValue[]
	 */
	public function getData(?Cursor $cursor = null): array
	{
		return ($this->dataCallback)(fn () => $this->decorate->getData($cursor));
	}

	public function getCount(): int
	{
		return ($this->countCallback)(fn () => $this->decorate->getCount());
	}

}
