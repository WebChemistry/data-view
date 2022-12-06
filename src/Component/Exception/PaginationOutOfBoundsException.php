<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Exception;

final class PaginationOutOfBoundsException extends \OutOfBoundsException
{

	public function __construct(string $message = 'Page is out of bounds.')
	{
		parent::__construct($message);
	}

}
