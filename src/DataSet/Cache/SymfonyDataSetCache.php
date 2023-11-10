<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSet\Cache;

use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use WebChemistry\DataView\Cursor\Cursor;
use WebChemistry\DataView\Cursor\OffsetCursor;

final class SymfonyDataSetCache implements DataSetCache
{

	public function __construct(
		private CacheInterface $cache,
		private string $namespace,
		private bool $onlyFirstPage = false,
		private ?int $expiration = null,
	)
	{
	}

	/**
	 * @template TValue
	 * @param callable(): TValue $callback
	 * @return TValue|null
	 */
	public function get(string $key, callable $callback, ?Cursor $cursor): mixed
	{
		if ($cursor === null) {
			return $this->cache->get(sprintf('%s:%s', $this->namespace, $key), function (CacheItemInterface $item) use ($callback): mixed {
				$item->expiresAfter($this->expiration);

				return $callback();
			});
		}

		if (!$cursor instanceof OffsetCursor) {
			return $callback();
		}

		if ($this->onlyFirstPage && $cursor->getOffset() !== 0) {
			return $callback();
		}

		return $this->cache->get(sprintf('%s:%s:%d', $this->namespace, $key, $cursor->getOffset()), function (CacheItemInterface $item) use ($callback): mixed {
			$item->expiresAfter($this->expiration);

			return $callback();
		});
	}

}
