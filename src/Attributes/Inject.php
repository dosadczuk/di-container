<?php
declare(strict_types=1);

namespace Container\Core\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class Inject
{
}