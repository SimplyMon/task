<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/MessageProcessor.php';

final class MessageProcessorTest extends TestCase
{
    public function testInspectionMappingWithValidDate()
    {
        $processor = new MessageProcessor();
        $messages = [
            [
                'description' => 'Please come for przegląd of AC',
                'dueDate' => '2025-10-07 14:00:00',
                'phone' => '123456789'
            ]
        ];

        $processor->processMessages($messages);
        $inspections = $processor->getInspections();

        $this->assertCount(1, $inspections);
        $inspection = $inspections[0];

        $this->assertEquals('inspection', $inspection->type);
        $this->assertEquals('scheduled', $inspection->status);
        $this->assertEquals('123456789', $inspection->clientPhone);
        $this->assertEquals('2025-10-07', $inspection->inspectionDate);
        $this->assertEquals(41, $inspection->weekOfYear);
    }

    public function testInspectionWithoutDueDate()
    {
        $processor = new MessageProcessor();
        $messages = [
            [
                'description' => 'Schedule an inspection for the AC units',
                'dueDate' => '',
                'phone' => '555-555'
            ]
        ];

        $processor->processMessages($messages);
        $inspections = $processor->getInspections();
        $inspection = $inspections[0];

        $this->assertEquals('', $inspection->inspectionDate);
        $this->assertNull($inspection->weekOfYear);
        $this->assertEquals('new', $inspection->status);
    }

    public function testFailureReportMappingWithPriority()
    {
        $processor = new MessageProcessor();
        $messages = [
            [
                'description' => 'This is bardzo pilne issue',
                'dueDate' => '2025-10-08',
                'phone' => '987654321'
            ]
        ];

        $processor->processMessages($messages);
        $failureReports = $processor->getFailureReports();

        $this->assertCount(1, $failureReports);
        $report = $failureReports[0];

        $this->assertEquals('failure_report', $report->type);
        $this->assertEquals('critical', $report->priority);
        $this->assertEquals('appointment', $report->status);
        $this->assertEquals('987654321', $report->clientPhone);
        $this->assertEquals('2025-10-08', $report->serviceVisitDate);
    }

    public function testFailureReportWithoutDueDate()
    {
        $processor = new MessageProcessor();
        $messages = [
            [
                'description' => 'Normal failure report',
                'dueDate' => '',
                'phone' => '111222333'
            ]
        ];

        $processor->processMessages($messages);
        $report = $processor->getFailureReports()[0];

        $this->assertEquals('', $report->serviceVisitDate);
        $this->assertEquals('new', $report->status);
        $this->assertEquals('normal', $report->priority);
    }

    public function testFailureReportHighPriority()
    {
        $processor = new MessageProcessor();
        $messages = [
            [
                'description' => 'This is pilne issue',
                'dueDate' => '2025-10-10',
                'phone' => ''
            ]
        ];

        $processor->processMessages($messages);
        $report = $processor->getFailureReports()[0];

        $this->assertEquals('high', $report->priority);
        $this->assertEquals('', $report->clientPhone);
    }

    public function testEmptyDescriptionIsSkipped()
    {
        $processor = new MessageProcessor();
        $messages = [
            ['description' => '', 'dueDate' => '2025-10-07', 'phone' => '1']
        ];

        $processor->processMessages($messages);

        $this->assertCount(0, $processor->getInspections());
        $this->assertCount(0, $processor->getFailureReports());
        $this->assertCount(1, $processor->getFailedMessages());
    }

    public function testDuplicateDescriptionIsSkipped()
    {
        $processor = new MessageProcessor();
        $messages = [
            ['description' => 'Duplicate', 'dueDate' => '2025-10-07', 'phone' => '1'],
            ['description' => 'Duplicate', 'dueDate' => '2025-10-07', 'phone' => '2']
        ];

        $processor->processMessages($messages);
        $this->assertCount(1, $processor->getFailureReports());
        $this->assertCount(1, $processor->getFailedMessages());
    }

    public function testJsonSerializableInspection()
    {
        $processor = new MessageProcessor();
        $messages = [
            ['description' => 'Inspection test', 'dueDate' => '', 'phone' => '']
        ];

        $processor->processMessages($messages);
        $inspection = $processor->getInspections()[0];

        $json = json_encode($inspection);
        $this->assertStringContainsString('"type":"inspection"', $json);
        $this->assertStringContainsString('"clientPhone":""', $json);
    }

    public function testJsonSerializableFailureReport()
    {
        $processor = new MessageProcessor();
        $messages = [
            ['description' => 'Failure test', 'dueDate' => '', 'phone' => '']
        ];

        $processor->processMessages($messages);
        $report = $processor->getFailureReports()[0];

        $json = json_encode($report);
        $this->assertStringContainsString('"type":"failure_report"', $json);
        $this->assertStringContainsString('"clientPhone":""', $json);
    }
}
