<?php

namespace App\Exceptions;

use InvalidArgumentException;

class InvalidAttendanceException extends InvalidArgumentException
{
    /**
     * Create an exception for missing association.
     */
    public static function missingAssociation(): self
    {
        return new self('La asistencia debe estar asociada a un ensayo o a un evento.');
    }

    /**
     * Create an exception for duplicate association.
     */
    public static function duplicateAssociation(): self
    {
        return new self('La asistencia no puede estar asociada a un ensayo y un evento simultáneamente.');
    }
}
