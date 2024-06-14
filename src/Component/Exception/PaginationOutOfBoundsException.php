<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component\Exception;

use Nette\Application\BadRequestException;

final class PaginationOutOfBoundsException extends BadRequestException
{

	public function __construct(string $message = 'Page is out of bounds.')
	{
		parent::__construct($message);
	}

}
