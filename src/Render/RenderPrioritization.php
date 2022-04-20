<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Render;

trait RenderPrioritization
{

	private int $renderPriority = 0;

	public function setRenderPriority(int $renderPriority): static
	{
		$this->renderPriority = RenderCollection::validatePriority($renderPriority, $this);

		return $this;
	}

	public function getRenderPriority(): int
	{
		return $this->renderPriority;
	}

}
