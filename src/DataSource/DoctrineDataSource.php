<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use WebChemistry\DataView\DataSet\IterableDataSet;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template T of object
 * @implements DataSource<T>
 */
final class DoctrineDataSource implements DataSource
{

	private QueryBuilder $queryBuilder;

	/** @var mixed[] */
	private array $options;

	private bool $compositeId;

	/**
	 * @param mixed[] $options
	 */
	public function __construct(QueryBuilder $queryBuilder, array $options = [])
	{
		$this->queryBuilder = $queryBuilder;
		$this->options = array_merge([
			'hydrationMode' => AbstractQuery::HYDRATE_OBJECT,
			'outputWalkers' => true,
			'fetchJoinCollection' => null,
		], $options);
	}

	/**
	 * @param DataViewComponent<T> $dataViewComponent
	 * @return IterableDataSet<T>
	 */
	public function getDataSet(DataViewComponent $dataViewComponent): IterableDataSet
	{
		$paginator = $this->createPaginator();

		return new IterableDataSet(
			$paginator->count(),
			$paginator->getIterator(),
		);
	}

	/**
	 * @return Paginator<T>
	 */
	protected function createPaginator(): Paginator
	{
		/** @phpstan-var string|AbstractQuery::HYDRATE_* $hydrateMode */
		$hydrateMode = $this->options['hydrationMode'];

		$query = $this->queryBuilder->getQuery()
			->setHydrationMode($hydrateMode);

		$fetchJoinCollection = $this->options['fetchJoinCollection'] === null ? !$this->isCompositeId() :
			(bool) $this->options['fetchJoinCollection'];

		return (new Paginator($query, $fetchJoinCollection))
			->setUseOutputWalkers((bool) $this->options['outputWalkers']);
	}

	protected function isCompositeId(): bool
	{
		if (!isset($this->compositeId)) {
			$this->compositeId = false;
			foreach ($this->queryBuilder->getRootEntities() as $entity) {
				if ($this->queryBuilder->getEntityManager()->getClassMetadata($entity)->isIdentifierComposite) {
					$this->compositeId = true;

					break;
				}
			}
		}

		return $this->compositeId;
	}

}
