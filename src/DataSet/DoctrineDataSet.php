<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet;

use ArrayIterator;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;
use LogicException;
use WebChemistry\DataView\Cursor\Cursor;
use WebChemistry\DataView\Cursor\LimitCursor;
use WebChemistry\DataView\Cursor\OffsetCursor;
use WebChemistry\DataView\Cursor\PointCursor;

/**
 * @template TValue
 * @implements DataSet<TValue>
 */
final class DoctrineDataSet implements DataSet
{

	/** @var (callable(Cursor $cursor, QueryBuilder $qb): void)|null */
	private $onCursor;

	private bool $compositeId;

	/**
	 * @param mixed[] $options
	 * @param (callable(Cursor $cursor, QueryBuilder $qb): void)|null $onCursor
	 */
	public function __construct(
		private QueryBuilder $queryBuilder,
		private array $options = [],
		?callable $onCursor = null,
	)
	{
		$this->onCursor = $onCursor;
	}

	/**
	 * @return ArrayIterator<array-key, TValue>
	 */
	public function getIterable(?Cursor $cursor = null): ArrayIterator
	{
		$limit = $cursor?->getLimit();
		$offset = null;

		if ($cursor instanceof OffsetCursor) {
			$offset = $cursor->getOffset();

		} else if ($cursor instanceof PointCursor) {
			if (!$this->onCursor) {
				throw new LogicException(sprintf('%s requires onCursor callback', PointCursor::class));
			}

		} else if (!$cursor instanceof LimitCursor && $cursor !== null) {
			throw new InvalidArgumentException(
				sprintf('Cursor must be instance of %s', implode('|', [OffsetCursor::class, LimitCursor::class, PointCursor::class]))
			);

		}

		if ($cursor && ($callback = $this->onCursor)) {
			$callback($cursor, $this->queryBuilder);
		}

		$paginator = $this->createPaginator();

		$paginator->getQuery()
			->setFirstResult($offset)
			->setMaxResults($limit);

		return $paginator->getIterator();
	}

	/**
	 * @return array<array-key, TValue>
	 */
	public function getData(?Cursor $cursor = null): array
	{
		return $this->getIterable($cursor)->getArrayCopy();
	}

	public function getCount(): int
	{
		return $this->createPaginator()->count();
	}

	/**
	 * @return Paginator<TValue>
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
