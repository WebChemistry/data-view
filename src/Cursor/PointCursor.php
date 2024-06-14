<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Cursor;

final class PointCursor implements Cursor
{

	/**
	 * @param int<0, max>|null $limit
	 */
	public function __construct(
		private ?int $limit,
		private string|null $pointer,
	)
	{
	}

	public function getUid(): string
	{
		return self::class . $this->limit . $this->pointer;
	}

	public function getPointer(): string|null
	{
		return $this->pointer;
	}

	public function getOffset(): ?int
	{
		return null;
	}

	/**
	 * @return int<0, max>|null
	 */
	public function getLimit(): ?int
	{
		return $this->limit;
	}

}
