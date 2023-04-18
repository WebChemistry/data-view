<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Cursor;

final class OffsetCursor implements Cursor
{

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

	public function getOffset(): int
	{
		return $this->offset;
	}

	public function getLimit(): ?int
	{
		return $this->limit;
	}
	
}
