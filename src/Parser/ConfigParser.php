<?php
declare(strict_types=1);

namespace Container\Core\Parser;

use Container\Core\Config;

/**
 * @internal
 */
interface ConfigParser {

    /**
     * @throws ConfigParserException
     */
    public function parse(): Config;
}