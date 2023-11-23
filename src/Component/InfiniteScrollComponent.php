<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\UI\Presenter;
use WebChemistry\DataView\Component\Template\InfiniteScrollTemplate;

/**
 * @template T
 * @extends ComponentWithPagination<T>
 */
final class InfiniteScrollComponent extends ComponentWithPagination
{

	use ComponentWithPublicTemplate;

	private string $caption = 'Load more';

	private ?string $class = null;

	private ?string $linkClass = null;

	public const TEMPLATES = [
		'default' => __DIR__ . '/templates/infiniteScroll/default.latte',
		'stimulus' => __DIR__ . '/templates/infiniteScroll/stimulus.latte',
	];

	public function __construct(
		int $itemsPerPage,
		private ?int $observeOffset = null,
	)
	{
		parent::__construct($itemsPerPage);

		$this->setFile(self::TEMPLATES['default']);
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

	public function setCaption(string $caption): static
	{
		$this->caption = $caption;

		return $this;
	}

	public function setObserveOffset(?int $observeOffset): static
	{
		$this->observeOffset = $observeOffset;

		return $this;
	}

	public function render(): void
	{
		/** @var InfiniteScrollTemplate<T> $template */
		$template = $this->createTemplate();
		$template->setFile($this->getFile());
		$template->nextLink = $this->getNextLink();
		$template->offset = $this->observeOffset;
		$template->nextLinkAjax = $this->getNextLink(true);
		$template->caption = $this->caption;
		$template->class = $this->class;
		$template->linkClass = $this->linkClass;

		$template->render();
	}

	public function getNextLink(?bool $ajax = null): ?string
	{
		if ($this->page >= $this->getPaginator()->getPageCount()) {
			return null;
		}

		if (!$ajax) {
			return $this->link('this', ['page' => $this->page + 1]);
		}

		return $this->link('paginate!', ['page' => $this->page + 1]);
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
		return InfiniteScrollTemplate::class;
	}

}
