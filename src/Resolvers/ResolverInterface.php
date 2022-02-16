<?php
declare(strict_types=1);

namespace Container\Core\Resolvers;

/**
 * @internal
 */
interface ResolverInterface
{
	public function resolve(): object;
}
