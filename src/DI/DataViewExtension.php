<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use OutOfBoundsException;
use stdClass;
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

		$def = $builder->addFactoryDefinition($this->prefix('component.factory.paginator'))
			->setImplement(PaginatorComponentFactory::class);

		$this->setFactoryTemplate($def, $config->components->paginator, PaginatorComponent::TEMPLATES);
	}

	/**
	 * @param array<string, string> $templates
	 */
	private function setFactoryTemplate(FactoryDefinition $def, stdClass $structure, array $templates = []): void
	{
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

		$def->getResultDefinition()
			->addSetup('setFile', [$file]);
	}

}
