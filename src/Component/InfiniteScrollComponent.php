<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\UI\Presenter;
use WebChemistry\DataView\Component\Template\InfiniteScrollTemplate;

/**
 * @template T
 * @extends BaseViewComponent<T>
 */
final class InfiniteScrollComponent extends BaseViewComponent
{

	use ComponentWithPublicTemplate;
	use PaginationComponent;

	private string $caption = 'Load more';

	private ?string $class = null;

	private ?string $linkClass = null;

	private ?string $tag;

	public const TEMPLATES = [
		'default' => __DIR__ . '/templates/infiniteScroll/default.latte',
	];

	public function __construct(
		private ?int $observeOffset = null,
	)
	{
		$this->setFile(self::TEMPLATES['default']);

		$this->redrawOnAjax();
	}

	public function setTag(?string $tag): static
	{
		$this->tag = $tag;

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
		$paginator = $this->getDataView()->getPaginator();

		/** @var InfiniteScrollTemplate<T> $template */
		$template = $this->createTemplate();
		$template->setFile($this->getFile());
		$template->nextLink = $paginator->getNextLink();
		$template->offset = $this->observeOffset;
		$template->nextLinkAjax = $paginator->getNextLink(true);
		$template->caption = $this->caption;
		$template->class = $this->class;
		$template->linkClass = $this->linkClass;
		$template->tag = $this->tag;

		$template->render();
	}

	public function formatTemplateClass(): string
	{
		return InfiniteScrollTemplate::class;
	}

}
