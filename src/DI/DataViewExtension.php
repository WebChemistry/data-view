<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DI;

use Nette\Application\Application;
use Nette\Application\UI\Presenter;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use OutOfBoundsException;
use stdClass;
use Throwable;
use WebChemistry\DataView\Component\Exception\PaginationOutOfBoundsException;
use WebChemistry\DataView\Component\Factory\InfiniteScrollComponentFactory;
use WebChemistry\DataView\Component\Factory\PaginatorComponentFactory;
use WebChemistry\DataView\Component\InfiniteScrollComponent;
use WebChemistry\DataView\Component\PaginatorComponent;

final class DataViewExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'components' => Expect::structure([
				'infiniteScroll' => Expect::structure([
					'file' => Expect::string(),
					'name' => Expect::string(),
					'tag' => Expect::string()->nullable(),
					'class' => Expect::string()->nullable(),
					'linkClass' => Expect::string()->nullable(),
					'caption' => Expect::string(),
				]),
				'paginator' => Expect::structure([
					'file' => Expect::string(),
					'name' => Expect::string(),
				]),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var stdClass $config */
		$config = $this->getConfig();

		$def = $builder->addFactoryDefinition($this->prefix('component.factory.infiniteScroll'))
			->setImplement(InfiniteScrollComponentFactory::class);

		$this->setFactoryTemplate($def, $config->components->infiniteScroll, InfiniteScrollComponent::TEMPLATES);
		$this->processInfiniteScroll($def, $config->components->infiniteScroll);

		$def = $builder->addFactoryDefinition($this->prefix('component.factory.paginator'))
			->setImplement(PaginatorComponentFactory::class);

		$this->setFactoryTemplate($def, $config->components->paginator, PaginatorComponent::TEMPLATES);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		if ($applicationName = $builder->getByType(Application::class)) {
			$application = $builder->getDefinition($applicationName);

			if ($application instanceof ServiceDefinition) {
				$application->addSetup('?->onError[] = [?, ?]', ['@self', self::class, 'onApplicationError']);
			}
		}
	}

	private function processInfiniteScroll(FactoryDefinition $def, stdClass $structure): void
	{
		$result = $def->getResultDefinition();

		if ($structure->class) {
			$result->addSetup('setClass', [$structure->class]);
		}

		if ($structure->linkClass) {
			$result->addSetup('setLinkClass', [$structure->linkClass]);
		}

		if (($caption = $structure->caption) !== null) {
			$result->addSetup('setCaption', [$caption]);
		}

		if (($tag = $structure->tag) !== null) {
			$result->addSetup('setTag', [$tag]);
		}
	}

	/**
	 * @param array<string, string> $templates
	 */
	private function setFactoryTemplate(FactoryDefinition $def, stdClass $structure, array $templates = []): void
	{
		$result = $def->getResultDefinition();

		if ($structure->name) {
			if (!isset($templates[$structure->name])) {
				throw new OutOfBoundsException(
					sprintf('Template %s not exists for %s.', $structure->name, $def->getImplement())
				);
			}

			$file = $templates[$structure->name];
		} elseif ($structure->file) {
			$file = $structure->file;
		} else {
			return;
		}

		$result->addSetup('setFile', [$file]);
	}

	public static function onApplicationError(Application $application, Throwable $error): void
	{
		if (
			$error instanceof PaginationOutOfBoundsException &&
			($presenter = $application->getPresenter()) &&
			$presenter instanceof Presenter &&
			$presenter->isAjax()
		) {
			$response = $presenter->getHttpResponse();
			$response->setCode(404);

			exit;
		}
	}

}
