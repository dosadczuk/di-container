<?php
declare(strict_types=1);

namespace Container\Core\Config;

use Container\Core\ContainerConfig;

interface ConfigParser {

    /**
     * @throws ConfigParserException
     */
    public function parse(): ContainerConfig;
}