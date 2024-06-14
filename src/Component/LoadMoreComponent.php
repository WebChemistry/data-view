<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use WebChemistry\DataView\Component\Template\LoadMoreComponentTemplate;

/**
 * @template T
 * @extends BaseViewComponent<T>
 */
final class LoadMoreComponent extends BaseViewComponent
{

	use ComponentWithPublicTemplate;
	use PaginationComponent;

	private string $caption = 'Load more';

	private ?string $class = null;

	private ?string $linkClass = null;

	public function __construct()
	{
		$this->setFile(__DIR__ . '/templates/loadMore/default.latte');

		$this->redrawOnAjax();
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
		$paginator = $this->getDataView()->getPaginator();

		/** @var LoadMoreComponentTemplate<T> $template */
		$template = $this->createTemplate();
		$template->setFile($this->getFile());
		$template->nextLink = $paginator->getNextLink();
		$template->nextLinkAjax = $paginator->getNextLink(true);
		$template->caption = $this->caption;
		$template->class = $this->class;
		$template->linkClass = $this->linkClass;

		$template->render();
	}

	public function formatTemplateClass(): string
	{
		return LoadMoreComponentTemplate::class;
	}

}
