<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Presenter;
use OutOfBoundsException;
use WebChemistry\DataView\Component\Object\Switcher;
use WebChemistry\DataView\Component\Template\SwitcherComponentTemplate;

/**
 * @template T
 * @extends BaseViewComponent<T>
 */
final class SwitcherComponent extends BaseViewComponent
{

	use ComponentWithAjax;

	#[Persistent]
	public ?string $val = null;

	/** @var Switcher[] */
	private array $switchers = [];

	public function addSwitcher(Switcher $switcher): static
	{
		$this->switchers[$switcher->getId()] = $switcher;

		return $this;
	}

	public function getSwitcher(string $name): Switcher
	{
		return $this->switchers[$name] ?? throw new OutOfBoundsException(sprintf('Switcher %s does not exists.', $name));
	}

	/**
	 * @return Switcher[]
	 */
	public function getSwitchers(): array
	{
		return $this->switchers;
	}

	public function render(): void
	{
		/** @var SwitcherComponentTemplate<T> $template */
		$template = $this->createTemplate(SwitcherComponentTemplate::class);
		$template->setFile(__DIR__ . '/templates/switcher/default.latte');
		$template->switchers = $this->switchers;
		$template->ajax = $this->isAjaxEnabled();

		$template->render();
	}

	/**
	 * @param mixed[] $params
	 */
	public function loadState(array $params): void
	{
		parent::loadState($params);

		foreach ($this->switchers as $switcher) {
			$switcher::injectValue($switcher, $switcher->getDefault());
		}

		if ($this->val) {
			foreach (explode(',', $this->val) as $name) {
				if (isset($this->switchers[$name])) {
					$switcher = $this->switchers[$name];

					$switcher::injectValue($switcher, !$switcher->getDefault());
				}
			}
		}

		foreach ($this->switchers as $switcher) {
			$switcher::injectLink($switcher, $this->switchLink($switcher));
		}
	}

	private function switchLink(Switcher $switcher): string
	{
		$values = [];
		foreach ($this->switchers as $item) {
			$value = $item->getValue();

			if ($item === $switcher) {
				$value = !$value;
			}

			if ($value !== $item->getDefault()) {
				$values[] = $item->getId();
			}
		}

		return $this->link($this->isAjaxEnabled() ? 'change!' : 'this', ['val' => implode(',', $values)]);
	}

	public function handleChange(): void
	{
		/** @var Presenter $presenter */
		$presenter = $this->getPresenter();

		if ($presenter->isAjax()) {
			$this->getDataView()->requestRedraw($this);
		}
	}

}
