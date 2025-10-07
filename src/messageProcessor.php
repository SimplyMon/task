<?php
require_once __DIR__ . '/inspection.php';
require_once __DIR__ . '/failure_report.php';

class MessageProcessor
{
    private array $inspections = [];
    private array $failureReports = [];
    private array $failedMessages = [];
    private array $descriptions = [];

    public function processMessages(array $messages)
    {
        foreach ($messages as $msg) {
            $desc = trim($msg['description'] ?? '');

            $phone = trim($msg['phone'] ?? '');
            $phone = ($phone === '' || $phone === '"') ? '' : $phone;

            if ($desc === '' || isset($this->descriptions[$desc])) {
                $this->failedMessages[] = [
                    'message' => $msg,
                    'reason' => 'Empty or duplicate description'
                ];
                echo "Skipped message: '{$desc}' - Empty or duplicate\n";
                continue;
            }

            $this->descriptions[$desc] = true;

            $type = (stripos($desc, 'przegląd') !== false || stripos($desc, 'inspection') !== false)
                ? 'inspection'
                : 'failure_report';

            if ($type === 'inspection') {
                $inspection = $this->mapInspection($msg, $phone);
                $this->inspections[] = $inspection;
                echo "Processed inspection: '{$desc}'\n";
            } else {
                $failure = $this->mapFailureReport($msg, $phone);
                $this->failureReports[] = $failure;
                echo "Processed failure report: '{$desc}'\n";
            }
        }
    }

    private function mapInspection(array $msg, string $phone): Inspection
    {
        $dueDate = $msg['dueDate'] ?? '';
        $dateObj = $this->parseDate($dueDate);

        return new Inspection([
            'description' => $msg['description'] ?? '',
            'type' => 'inspection',
            'inspectionDate' => $dateObj ? $dateObj->format('Y-m-d') : '',
            'weekOfYear' => $dateObj ? (int)$dateObj->format('W') : null,
            'status' => $dateObj ? 'scheduled' : 'new',
            'recommendations' => '',
            'clientPhone' => $phone,
            'createdAt' => date('Y-m-d H:i:s'),
        ]);
    }

    private function mapFailureReport(array $msg, string $phone): FailureReport
    {
        $desc = $msg['description'] ?? '';

        $priority = 'normal';
        if (stripos($desc, 'bardzo pilne') !== false) {
            $priority = 'critical';
        } elseif (stripos($desc, 'pilne') !== false) {
            $priority = 'high';
        }

        $dueDate = $msg['dueDate'] ?? '';
        $dateObj = $this->parseDate($dueDate);

        return new FailureReport([
            'description' => $desc,
            'type' => 'failure_report',
            'priority' => $priority,
            'serviceVisitDate' => $dateObj ? $dateObj->format('Y-m-d') : '',
            'status' => $dateObj ? 'appointment' : 'new',
            'serviceNotes' => '',
            'clientPhone' => $phone,
            'createdAt' => date('Y-m-d H:i:s'),
        ]);
    }

    private function parseDate($dateStr)
    {
        if (!$dateStr) return null;
        try {
            return new DateTime($dateStr);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getInspections(): array
    {
        return $this->inspections;
    }

    public function getFailureReports(): array
    {
        return $this->failureReports;
    }

    public function getFailedMessages(): array
    {
        return $this->failedMessages;
    }
}
