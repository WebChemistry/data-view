<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use LogicException;
use Nette\Application\UI\Presenter;
use WebChemistry\DataView\Component\Template\PaginatorComponentTemplate;

/**
 * @template T
 * @extends ComponentWithPagination<T>
 */
final class PaginatorComponent extends ComponentWithPagination
{

	use ComponentWithAjax;
	use ComponentWithPublicTemplate;

	public const TEMPLATES = [
		'default' => __DIR__ . '/templates/paginator/default.latte',
	];

	public function __construct(int $itemsPerPage)
	{
		parent::__construct($itemsPerPage);

		$this->setFile(self::TEMPLATES['default']);
	}

	public function render(): void
	{
		/** @var PaginatorComponentTemplate<T> $template */
		$template = $this->createTemplate();
		$template->setFile($this->getFile());

		$paginator = $this->getPaginator();

		$pageCount = $paginator->getPageCount();

		if ($pageCount === null) {
			throw new LogicException('Page count is null, item count for paginator is not set.');
		}

		$template->previousLink = $this->getPreviousLink();
		$template->nextLink = $this->getNextLink();
		$template->page = $this->page;
		$template->pageCount = $pageCount;
		$template->steps = $this->getSteps();
		$template->ajax = $this->isAjaxEnabled();

		$template->render();
	}

	public function handlePaginate(): void
	{
		/** @var Presenter $presenter */
		$presenter = $this->getPresenter();

		if ($presenter->isAjax()) {
			$this->getDataView()->requestRedraw($this);

			$this->redrawControl();
		}
	}

	public function getPreviousLink(): ?string
	{
		if ($this->page > 1) {
			return $this->stepLink($this->page - 1);
		}

		return null;
	}

	public function getNextLink(?bool $ajax = null): ?string
	{
		if ($this->page < $this->getPaginator()->getPageCount()) {
			return $this->stepLink($this->page + 1, $ajax);
		}

		return null;
	}

	public function formatTemplateClass(): string
	{
		return PaginatorComponentTemplate::class;
	}

	/**
	 * @internal
	 */
	public function stepLink(int $page, ?bool $ajax = null): string
	{
		return $this->link($ajax ?? $this->isAjaxEnabled() ? 'paginate!' : 'this', ['page' => $page]);
	}

}
