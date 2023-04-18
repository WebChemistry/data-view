<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Cursor;

final class OffsetCursor implements Cursor
{

	/**
	 * @param int<0, max> $offset
	 * @param int<0, max>|null $limit
	 */
	public function __construct(
		private int $offset,
		private ?int $limit,
	)
	{
	}

	public function getUid(): string
	{
		return self::class . $this->offset . $this->limit;
	}

	/**
	 * @return int<0, max>
	 */
	public function getOffset(): int
	{
		return $this->offset;
	}

	/**
	 * @return int<0, max>|null
	 */
	public function getLimit(): ?int
	{
		return $this->limit;
	}
	
}
