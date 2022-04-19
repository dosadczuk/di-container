<?php
declare(strict_types=1);

namespace Container;

final class Config
{
    /**
     * Path to the configuration file.
     */
    public static string $file_path = '/container.php';

    /**
     * @var Dependency[]
     *
     * @internal
     */
    private array $dependencies = [];

    /**
     * @internal
     */
    public static function find(): self
    {
        $self = new self();
        if (!file_exists(self::$file_path)) {
            return $self;
        }

        if (is_callable($prepare = include self::$file_path)) {
            $prepare($self);
        }

        return $self;
    }

    /**
     * Add abstract with definition to container.
     *
     * @template T
     *
     * @param class-string<T> $abstract Abstract/Interface.
     * @param string|\Closure $definition Implementation or factory function.
     *
     * @api
     */
    public function add(string $abstract, string|\Closure $definition): void
    {
        $this->dependencies[] = Dependency::transient($abstract, $definition);
    }

    /**
     * Add abstract with definition to container, as singleton.
     *
     * @template T
     *
     * @param class-string<T> $abstract Abstract/Interface.
     * @param null|string|\Closure $definition Optional implementation or factory function.
     *
     * @api
     */
    public function addShared(string $abstract, string|\Closure $definition = null): void
    {
        $this->dependencies[] = Dependency::shared($abstract, $definition);
    }

    /**
     * @return Dependency[]
     *
     * @internal
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }
}
