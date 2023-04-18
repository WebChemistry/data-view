<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Cursor;

interface Cursor
{

	public function getUid(): string;

	/**
	 * @return int<0, max>|null
	 */
	public function getLimit(): ?int;

}
