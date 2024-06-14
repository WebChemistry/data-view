<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use LogicException;
use Nette\Application\UI\Presenter;
use WebChemistry\DataView\Component\Template\PaginatorComponentTemplate;
use WebChemistry\DataView\Paginator\OffsetPaginator;
use WebChemistry\DataView\Paginator\PaginatorStepper;

/**
 * @template T
 * @extends BaseViewComponent<T>
 */
final class PaginatorComponent extends BaseViewComponent
{

	use ComponentWithAjax;
	use ComponentWithPublicTemplate;
	use PaginationComponent;

	public const TEMPLATES = [
		'default' => __DIR__ . '/templates/paginator/default.latte',
	];

	public function __construct()
	{
		$this->setFile(self::TEMPLATES['default']);

		$this->redrawOnAjax();
	}

	public function render(): void
	{
		/** @var PaginatorComponentTemplate<T> $template */
		$template = $this->createTemplate();
		$template->setFile($this->getFile());

		$paginator = $this->getDataView()->getPaginator();

		if (!$paginator instanceof OffsetPaginator) {
			throw new LogicException(sprintf('Paginator must be instance of %s, %s given.', OffsetPaginator::class, get_debug_type($paginator)));
		}

		$template->previousLink = $paginator->getPrevLink($this->isAjaxEnabled());
		$template->nextLink = $paginator->getNextLink($this->isAjaxEnabled());
		$template->page = $paginator->page;
		$template->steps = $paginator->getSteps();
		$template->ajax = $this->isAjaxEnabled();
		$template->stepLink = fn (int $page) => $paginator->getLink($page, $this->isAjaxEnabled());

		$template->render();
	}

	public function formatTemplateClass(): string
	{
		return PaginatorComponentTemplate::class;
	}

}
