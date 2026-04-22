<?php
// app/Exceptions/IncidentLockedException.php
namespace App\Exceptions;

class IncidentLockedException extends BusinessRuleException
{
    public function __construct()
    {
        parent::__construct("Cet incident est clôturé ou archivé et ne peut plus être modifié.");
    }
}
