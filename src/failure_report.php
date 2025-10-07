<?php
class FailureReport implements JsonSerializable
{
    public string $description;
    public string $type = 'failure_report';
    public string $priority = 'normal';
    public string $serviceVisitDate = '';
    public string $status;
    public string $serviceNotes = '';
    public string $clientPhone = '';
    public string $createdAt;

    public function __construct(array $data)
    {
        $this->description = $data['description'] ?? '';
        $this->priority = $data['priority'] ?? 'normal';
        $this->serviceVisitDate = $data['serviceVisitDate'] ?? '';
        $this->status = $data['status'] ?? 'new';
        $this->serviceNotes = $data['serviceNotes'] ?? '';
        $this->clientPhone = $data['clientPhone'] ?? '';
        $this->createdAt = $data['createdAt'] ?? date('Y-m-d H:i:s');
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
