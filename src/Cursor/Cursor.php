<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Cursor;

interface Cursor
{

	public function getUid(): string;
	
	public function getLimit(): ?int;

}
