<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\UI\Presenter;
use WebChemistry\DataView\Component\Template\LoadMoreComponentTemplate;

/**
 * @template T
 * @extends ComponentWithPagination<T>
 */
final class LoadMoreComponent extends ComponentWithPagination
{

	use ComponentWithPublicTemplate;

	private string $caption = 'Load more';

	private ?string $class = null;

	private ?string $linkClass = null;

	public function __construct(int $itemsPerPage)
	{
		parent::__construct($itemsPerPage);

		$this->setFile(__DIR__ . '/templates/loadMore/default.latte');
	}

	public function setCaption(string $caption): static
	{
		$this->caption = $caption;

		return $this;
	}

	public function setClass(?string $class): static
	{
		$this->class = $class;

		return $this;
	}

	public function setLinkClass(?string $linkClass): static
	{
		$this->linkClass = $linkClass;

		return $this;
	}

	public function render(): void
	{
		/** @var LoadMoreComponentTemplate<T> $template */
		$template = $this->createTemplate();
		$template->setFile($this->getFile());
		$template->nextLink = $this->getNextLink();
		$template->nextLinkAjax = $this->getNextLinkAjax();
		$template->caption = $this->caption;
		$template->class = $this->class;
		$template->linkClass = $this->linkClass;

		$template->render();
	}

	public function getNextLink(): ?string
	{
		if ($this->page < $this->getPaginator()->getPageCount()) {
			return $this->link('this', ['page' => $this->page + 1]);
		}

		return null;
	}

	public function getNextLinkAjax(): ?string
	{
		if ($this->page < $this->getPaginator()->getPageCount()) {
			return $this->link('paginate!', ['page' => $this->page + 1]);
		}

		return null;
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

	public function formatTemplateClass(): string
	{
		return LoadMoreComponentTemplate::class;
	}

}
