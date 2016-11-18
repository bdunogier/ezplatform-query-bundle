<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace BD\EzPlatformQueryBundle\Exception;

use InvalidArgumentException;

class InvalidTypeException extends InvalidArgumentException
{
    /**
     * @param string $subjectName The incorrect thing's name. Ex: 'ValueObject'
     * @param mixed $subject The subject itself. Ex: a ValueObject.
     * @param string $expected The type that was expected.
     */
    public function __construct($subjectName, $subject, $expected)
    {
        parent::__construct(
            "Invalid type for $subjectName. Expected $expected, got " .
            is_object($subject) ? get_class($subject) : gettype($subject)
        );
    }
}
