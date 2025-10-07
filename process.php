<?php
require_once __DIR__ . '/src/MessageProcessor.php';

// ---------------------------
// Parse CLI arguments
// ---------------------------
if ($argc < 2) {
    echo "Usage: php process.php path/to/input.json [--out-dir=path/to/output]\n";
    exit(1);
}

$inputFile = $argv[1];
$outDir = __DIR__ . '/output';
if (!is_dir($outDir)) {
    mkdir($outDir, 0777, true);
}


// Check for optional --out-dir argument
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--out-dir=')) {
        $outDir = substr($arg, 10);
    }
}

// ---------------------------
// Validate input file
// ---------------------------
if (!file_exists($inputFile)) {
    echo "Input file does not exist: {$inputFile}\n";
    exit(1);
}

// ---------------------------
// Create output directory if missing
// ---------------------------
if (!is_dir($outDir)) {
    mkdir($outDir, 0777, true);
    echo "Created output directory: {$outDir}\n";
}

// ---------------------------
// Read input JSON
// ---------------------------
$json = file_get_contents($inputFile);
$messages = json_decode($json, true);

if ($messages === null) {
    echo "Failed to parse JSON: " . json_last_error_msg() . "\n";
    exit(1);
}

// ---------------------------
// Process messages
// ---------------------------
echo "Processing " . count($messages) . " messages...\n";
$processor = new MessageProcessor();
$processor->processMessages($messages);

// ---------------------------
// Write output files
// ---------------------------
$inspectionsFile = $outDir . '/inspections.json';
$failureReportsFile = $outDir . '/failure_reports.json';
$failedMessagesFile = $outDir . '/failed_messages.json';

file_put_contents($inspectionsFile, json_encode($processor->getInspections(), JSON_PRETTY_PRINT));
file_put_contents($failureReportsFile, json_encode($processor->getFailureReports(), JSON_PRETTY_PRINT));
file_put_contents($failedMessagesFile, json_encode($processor->getFailedMessages(), JSON_PRETTY_PRINT));

echo "Output files written:\n";
echo " - {$inspectionsFile}\n";
echo " - {$failureReportsFile}\n";
echo " - {$failedMessagesFile}\n";

// ---------------------------
// Summary
// ---------------------------
echo "\n=== Processing Summary ===\n";
echo "total processed messages: " . count($messages) . "\n";
echo "number of inspections created: " . count($processor->getInspections()) . "\n";
echo "number of failure reports created: " . count($processor->getFailureReports()) . "\n";
echo "number of messages not processed: " . count($processor->getFailedMessages()) . "\n";

// Optional: show reasons for failed messages
if (count($processor->getFailedMessages()) > 0) {
    echo "Reasons for skipped messages:\n";
    foreach ($processor->getFailedMessages() as $failed) {
        $reason = $failed['reason'] ?? 'unknown';
        $desc = trim($failed['message']['description'] ?? '');
        echo " - '{$desc}' : {$reason}\n";
    }
}
