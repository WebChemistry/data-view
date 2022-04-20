<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Render;

interface RenderWithPriority
{

	public const LOWEST_PRIORITY = -100;
	public const LOW_PRIORITY = -50;
	public const NORMAL_PRIORITY = 0;
	public const HIGH_PRIORITY = 50;
	public const HIGHEST_PRIORITY = 100;

	public function getRenderPriority(): int;

}
