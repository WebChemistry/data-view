<?php declare(strict_types = 1);

namespace WebChemistry\DataView\Component;

use Nette\Application\UI\Form;
use WebChemistry\DataView\Component\Template\FormComponentTemplate;

/**
 * @template T
 * @extends BaseViewComponent<T>
 */
final class FormComponent extends BaseViewComponent
{

	/** @var mixed[] */
	private array $values = [];

	public function __construct(
		private Form $form,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public function getValues(): array
	{
		return $this->values;
	}

	protected function createComponentForm(): Form
	{
		$this->form->onSuccess[] = function (array $values): void {
			$this->values = $values;

			$this->redirect('this');
		};

		return $this->form;
	}

	public function render(): void
	{
		/** @var FormComponentTemplate<T> $template */
		$template = $this->createTemplate(FormComponentTemplate::class);
		$template->setFile(__DIR__ . '/templates/form/default.latte');

		$template->render();
	}

	public function loadState(array $params): void
	{
		$this->values = $params;
	}

	public function saveState(array &$params): void
	{
		$params = $this->values;
	}

}
