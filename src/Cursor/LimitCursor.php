<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Cursor;

final class LimitCursor implements Cursor
{

	public function __construct(
		private ?int $limit,
	)
	{
	}

	public function getUid(): string
	{
		return self::class . $this->limit;
	}

	public function getLimit(): ?int
	{
		return $this->limit;
	}

}
