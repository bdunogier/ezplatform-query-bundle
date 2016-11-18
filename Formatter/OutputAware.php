<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace BD\EzPlatformQueryBundle\Formatter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * A formatter that accepts an OutputInterface.
 */
interface OutputAware
{
    public function setOutput(OutputInterface $output);
}
