<?php declare(strict_types = 1);

namespace WebChemistry\DataView\DataSource;

use Closure;
use LogicException;
use Nette\Utils\Type;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use WebChemistry\DataView\DataSet\DataSet;
use WebChemistry\DataView\DataViewComponent;

/**
 * @template T
 * @implements DataSource<T>
 */
final class InjectableDataSource implements DataSource
{

	/** @var callable */
	private $callable;

	/** @var mixed[] */
	private array $autowire;

	/**
	 * @param callable $callable
	 */
	public function __construct(callable $callable, mixed ... $autowire)
	{
		$this->callable = $callable;
		$this->autowire = $autowire;
	}

	/**
	 * @param DataViewComponent<T> $component
	 * @return DataSet<T>
	 */
	public function getDataSet(DataViewComponent $component): DataSet
	{
		$source = ($this->callable)(...$this->getAutowiredArguments($component, $this->autowire));

		if (!$source instanceof DataSource) {
			throw new LogicException(
				sprintf(
					'Callback of CallableDataSource must returns %s, %s returned.',
					DataSource::class,
					get_debug_type($source)
				)
			);
		}

		return $source->getDataSet($component); // @phpstan-ignore-line
	}

	/**
	 * @param DataViewComponent<T> $component
	 * @param mixed[] $autowire
	 * @return mixed[]
	 */
	private function getAutowiredArguments(DataViewComponent $component, array $autowire): array
	{
		$reflection = $this->getReflectionFromCallable();
		$arguments = [];
		foreach ($reflection->getParameters() as $parameter) {
			$name = $parameter->getName();
			$type = $parameter->getType();
			if ($type instanceof ReflectionNamedType && $type->getName() === DataViewComponent::class) {
				$arguments[] = $component;
			} else if (array_key_exists($name, $autowire)) {
				$arguments[] = $autowire[$name];

				unset($autowire[$name]);
			} else if ($arg = $component->getComponent($name, false)) {
				$type = Type::fromReflection($parameter);
				if ($type?->allows($arg::class) === false) {
					throw new LogicException(
						sprintf(
							'%s: Autowired parameter %s expects to be %s, component of type %s would be passed.',
							self::class,
							$name,
							(string) $type,
							get_debug_type($arg)
						)
					);
				}

				$arguments[] = $arg;
			} else if ($parameter->isDefaultValueAvailable()) {
				$arguments[] = $parameter->getDefaultValue();
			} else {
				throw new LogicException(
					sprintf('%s: Component or value for autowired parameter %s not found.', self::class, $name)
				);
			}
		}

		if ($autowire) {
			throw new LogicException(
				sprintf('%s: Unused autowired arguments: %s', self::class, implode(', ', array_keys($autowire)))
			);
		}

		return $arguments;
	}

	private function getReflectionFromCallable(): ReflectionFunction|ReflectionMethod
	{
		if ($this->callable instanceof Closure) {
			return new ReflectionFunction($this->callable);
		}

		if (is_string($this->callable) && function_exists($this->callable)) {
			return new ReflectionFunction($this->callable);
		}

		if (is_string($this->callable) && str_contains($this->callable, '::')) {
			return new ReflectionMethod($this->callable);
		}

		if (is_array($this->callable)) {
			return new ReflectionMethod($this->callable[0], $this->callable[1]);
		}

		throw new LogicException('Invalid callable given.');
	}

}
