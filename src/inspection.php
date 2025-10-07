<?php
class Inspection implements JsonSerializable
{
    public string $description;
    public string $type = 'inspection';
    public string $inspectionDate = '';
    public ?int $weekOfYear = null;
    public string $status;
    public string $recommendations = '';
    public string $clientPhone = '';
    public string $createdAt;

    public function __construct(array $data)
    {
        $this->description = $data['description'] ?? '';
        $this->inspectionDate = $data['inspectionDate'] ?? '';
        $this->weekOfYear = $data['weekOfYear'] ?? null;
        $this->status = $data['status'] ?? 'new';
        $this->recommendations = $data['recommendations'] ?? '';
        $this->clientPhone = $data['clientPhone'] ?? '';
        $this->createdAt = $data['createdAt'] ?? date('Y-m-d H:i:s');
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
