<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\UI\Control;
use WebChemistry\DataView\Paginator\Paginator;

trait PaginationComponent
{

	public function redrawOnAjax(?string $snippet = null, bool $redraw = true): void
	{
		$this->onAnchor[] = function () use ($snippet, $redraw): void {
			$this->getDataView()->onRedrawRequest[] = function (Control $control) use ($snippet, $redraw): void {
				if ($control instanceof Paginator) {
					$this->redrawControl($snippet, $redraw);
				}
			};
		};
	}

}
