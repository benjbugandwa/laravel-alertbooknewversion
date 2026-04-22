<?php

namespace App\Mail;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IncidentAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Incident $incident,
        public string $superviseurName,
        public string $assignedByName,
        public string $provinceName = '-',
        public string $territoireName = '-',
        public string $zoneName = '-',
        public string $actionUrl = ''
    ) {}

    public function build(): self
    {
        return $this->subject("Incident assigné — {$this->incident->code_incident}")
            ->view('emails.incident-assigned')
            ->with([
                'superviseurName' => $this->superviseurName,
                'assignedByName' => $this->assignedByName,
                'codeIncident' => $this->incident->code_incident,
                'statut' => $this->incident->statut_incident ?? 'En attente',
                'severite' => $this->incident->severite ?? '-',
                'dateIncident' => optional($this->incident->date_incident)->format('Y-m-d'),
                'province' => $this->provinceName,
                'territoire' => $this->territoireName,
                'zoneSante' => $this->zoneName,
                'localite' => $this->incident->localite ?? '-',
                'actionUrl' => $this->actionUrl,
            ]);
    }
}
