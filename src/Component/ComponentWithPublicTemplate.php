<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

trait ComponentWithPublicTemplate
{

	private string $file;

	public function setFile(string $file): static
	{
		$this->file = $file;

		return $this;
	}

	public function getFile(): string
	{
		return $this->file;
	}

}
