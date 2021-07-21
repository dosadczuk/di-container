<?php
declare(strict_types=1);

namespace Container\Core\Config\Parser;

use Container\Core\Config\Config;

/**
 * @internal
 */
interface ConfigParser {

    /**
     * @throws ConfigParserException
     */
    public function parse(): Config;
}