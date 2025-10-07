# 🧰 Recruitment Task – PHP Message Processor

This project processes a list of service messages (in JSON format) and classifies them into:

- **Inspections**
- **Failure Reports**
- **Failed Messages** (duplicates or empty descriptions)

It outputs the results as three JSON files under the `/out` directory.

---

## Project Structure

recruitment-task/
├── src/
│ ├── failure_report.php
│ ├── inspection.php
│ └── messageProcessor.php
│


├── tests/
│ └── messageProcessorTest.php
│
├── out/
│
├── process.php
├── recruitment-task-source.json
├── composer.json
└── README.md

---

## Requirements

Before running the project, make sure you have:

- **PHP ≥ 8.2**
- **Composer**

You can check your versions with:

```bash
php -v
composer -V
```

---

## Installation

Clone the repository and install dependencies:

git clone https://github.com/SimplyMon/task.git
cd recruitment-task
composer install

---

## Running the Processor

Run the main processing script:

php process.php recruitment-task-source.json

This will process all messages and create the following output files:
output/
├── inspections.json
├── failure_reports.json
└── failed_messages.json

Example console output:

Processing 20 messages...
Processed inspection: 'Please come for an inspection of the electrical installation on February 15.'
Processed failure report: 'Boiler failure in the main storage room.'
...
=== Processing Summary ===
Total messages: 20
Inspections created: 7
Failure reports created: 11
Messages not processed: 2

---

## Running Unit Tests

This project includes PHPUnit tests.

To run all tests:

./vendor/bin/phpunit --testdox tests

Example output:

Message Processor
✔ Inspection mapping with valid date
✔ Failure report mapping with priority
✔ Duplicate description is skipped
✔ Empty description is skipped
✔ JSON serializable inspection
✔ JSON serializable failure report

---

# 👨‍💻 Author

Hi there! 👋  
I'm **Simon Pasag**, a Web Developer.

---

## 📬 Contact

💼 **Portfolio:** [https://mondev.vercel.app/](https://mondev.vercel.app/)  
💻 **GitHub:** [https://github.com/SimplyMon](https://github.com/SimplyMon)  
✉️ **Email:** mon.dev005@gmail.com  
🔗 **LinkedIn:** [https://www.linkedin.com/in/simon-expression-pasag-85b1112b7/](https://www.linkedin.com/in/simon-expression-pasag-85b1112b7/)

---

_© 2025 Simon Pasag. All rights reserved._
