<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

trait ComponentWithAjax
{

	private bool $ajax = false;

	public function enableAjax(): static
	{
		$this->ajax = true;

		return $this;
	}

	public function disableAjax(): static
	{
		$this->ajax = true;

		return $this;
	}

	public function isAjaxEnabled(): bool
	{
		return $this->ajax;
	}

}
