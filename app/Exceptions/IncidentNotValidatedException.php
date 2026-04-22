<?php
// app/Exceptions/IncidentNotValidatedException.php
namespace App\Exceptions;

class IncidentNotValidatedException extends BusinessRuleException
{
    public function __construct()
    {
        parent::__construct("L’incident doit être validé avant cette action.");
    }
}
