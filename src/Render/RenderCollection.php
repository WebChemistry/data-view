<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Render;

use LogicException;
use Nette\Application\UI\Renderable;

final class RenderCollection
{

	/**
	 * @param iterable<array-key, Renderable> $controls
	 */
	public function __construct(
		private iterable $controls,
	)
	{
	}

	/**
	 * @return iterable<int, Renderable>
	 */
	public function getSortedCollectors(): iterable
	{
		$priority = [];

		foreach ($this->controls as $control) {
			if ($control instanceof RenderWithPriority) {
				$priority[self::validatePriority($control->getRenderPriority(), $control)][] = $control;
			} else {
				$priority[0][] = $control;
			}
		}

		krsort($priority, SORT_NUMERIC);

		foreach ($priority as $items) {
			foreach ($items as $item) {
				yield $item;
			}
		}
	}

	public static function validatePriority(int $priority, ?object $object = null): int
	{
		if ($priority < -100 || $priority > 100) {
			throw new LogicException(
				sprintf(
					'Priority must be between -100 and 100, %d given%s.',
					$priority,
					$object === null ? '' : sprintf(' of object %s', $object::class),
				)
			);
		}

		return $priority;
	}

}
