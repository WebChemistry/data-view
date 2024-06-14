<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Paginator;

use Nette\Application\UI\Renderable;
use Nette\Application\UI\SignalReceiver;
use Nette\Application\UI\StatePersistent;
use Nette\ComponentModel\IContainer;
use WebChemistry\DataView\Component\LogicViewComponent;
use WebChemistry\DataView\Cursor\Cursor;

interface Paginator extends Renderable, SignalReceiver, StatePersistent, IContainer, LogicViewComponent
{

	public function createCurrentCursor(): Cursor;

	public function isFirstPage(): bool;

	public function isLastPage(): bool;

	public function getNextLink(bool $ajax = false): ?string;

	public function getPrevLink(bool $ajax = false): ?string;

}
