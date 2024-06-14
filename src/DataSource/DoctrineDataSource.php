<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use WebChemistry\DataView\Cursor\Cursor;
use WebChemistry\DataView\DataSet\CacheableDataSet;
use WebChemistry\DataView\DataSet\DataSet;
use WebChemistry\DataView\DataSet\DoctrineDataSet;
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

	/** @var (callable(Cursor $cursor, QueryBuilder $qb): void)|null */
	private $onCursor;

	/**
	 * @param mixed[] $options
	 * @param (callable(Cursor $cursor, QueryBuilder $qb): void)|null $onCursor
	 */
	public function __construct(QueryBuilder $queryBuilder, array $options = [], ?callable $onCursor = null)
	{
		$this->queryBuilder = $queryBuilder;
		$this->options = array_merge([
			'hydrationMode' => AbstractQuery::HYDRATE_OBJECT,
			'outputWalkers' => true,
			'fetchJoinCollection' => null,
		], $options);
		$this->onCursor = $onCursor;
	}

	/**
	 * @param DataViewComponent<T> $component
	 * @return DataSet<T>
	 */
	public function getDataSet(DataViewComponent $component): DataSet
	{
		return new CacheableDataSet(new DoctrineDataSet($this->queryBuilder, $this->options, $this->onCursor));
	}

}
