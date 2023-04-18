<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Cursor;

final class LimitCursor implements Cursor
{

	/**
	 * @param int<0, max>|null $limit
	 */
	public function __construct(
		private ?int $limit,
	)
	{
	}

	public function getUid(): string
	{
		return self::class . $this->limit;
	}

	/**
	 * @return int<0, max>|null
	 */
	public function getLimit(): ?int
	{
		return $this->limit;
	}

}
