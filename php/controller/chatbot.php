<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['emp_id'])) {
    http_response_code(401);
    echo json_encode(['reply' => "You need to be logged in to use the assistant."]);
    exit();
}

require_once '../dbcon/dbcon.php';

$emp_id = $_SESSION['emp_id'];

// ---- Get the incoming message ----
$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');

if ($message === '') {
    echo json_encode(['reply' => "Please type a question."]);
    exit();
}

try {
    // ---- Fetch this employee's own record only ----
    $stmt = $pdo->prepare("SELECT * FROM employee WHERE emp_id = ?");
    $stmt->execute([$emp_id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        echo json_encode(['reply' => "I couldn't find your employee record."]);
        exit();
    }

    // ---- Load FAQ intents ----
    $faqStmt = $pdo->query("SELECT * FROM chat_faq");
    $faqs = $faqStmt->fetchAll(PDO::FETCH_ASSOC);

    $messageLower = strtolower($message);
    $matchedFaq = null;

    foreach ($faqs as $faq) {
        $examples = array_map('trim', explode(',', strtolower($faq['question_examples'])));
        foreach ($examples as $example) {
            if ($example !== '' && strpos($messageLower, $example) !== false) {
                $matchedFaq = $faq;
                break 2;
            }
        }
    }

    if (!$matchedFaq) {
        echo json_encode([
            'reply' => "Sorry, I didn't quite get that. You can ask me things like " .
                       "\"when is my salary\", \"how much is my salary\", \"is my salary released\", " .
                       "or \"do I have incentives\"."
        ]);
        exit();
    }

    // ---- Build replacement values ----
    $name   = $employee['name'] ?? trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? ''));
    $salary = number_format((float)($employee['salary'] ?? 0), 2);
    $status = $employee['status'] ?? 'pending';
    $didRequest = $employee['didRequest'] ?? 'N/A';
    $currentIncentives = $employee['currentIncentives'] ?? 'None';

    // ---- Pay schedule (from payroll_settings) ----
    $settingsStmt = $pdo->query("SELECT * FROM payroll_settings LIMIT 1");
    $settings = $settingsStmt->fetch(PDO::FETCH_ASSOC);

    $pay_frequency = $settings['pay_frequency'] ?? 'semimonthly';
    $pay_dates_raw = $settings['pay_dates'] ?? '15,30';
    $next_pay_date = calculateNextPayDate($pay_dates_raw);

    // ---- Fill the template ----
    $reply = $matchedFaq['answer_template'];
    $replacements = [
        '{name}' => htmlspecialchars($name),
        '{salary}' => $salary,
        '{status}' => htmlspecialchars($status),
        '{didRequest}' => htmlspecialchars($didRequest),
        '{currentIncentives}' => htmlspecialchars($currentIncentives),
        '{pay_frequency}' => htmlspecialchars($pay_frequency),
        '{pay_dates}' => htmlspecialchars($pay_dates_raw),
        '{next_pay_date}' => $next_pay_date,
    ];
    $reply = strtr($reply, $replacements);

    echo json_encode(['reply' => $reply]);

} catch (PDOException $e) {
    error_log("Chatbot error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['reply' => "Sorry, something went wrong on my end. Please try again later."]);
}

/**
 * Given a comma-separated list of day-of-month pay dates (e.g. "15,30"),
 * find the next upcoming pay date from today.
 */
function calculateNextPayDate(string $payDatesRaw): string {
    $days = array_map('intval', array_map('trim', explode(',', $payDatesRaw)));
    sort($days);

    $today = new DateTime('today');
    $currentDay = (int)$today->format('d');
    $currentMonth = (int)$today->format('m');
    $currentYear = (int)$today->format('Y');

    foreach ($days as $day) {
        if ($day >= $currentDay) {
            $candidate = DateTime::createFromFormat('Y-m-d', "$currentYear-$currentMonth-$day");
            if ($candidate !== false) {
                return $candidate->format('F j, Y');
            }
        }
    }

    // No remaining pay date this month -> use first pay date of next month
    $nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;
    $nextYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;
    $firstDay = $days[0];
    $candidate = DateTime::createFromFormat('Y-m-d', "$nextYear-$nextMonth-$firstDay");
    return $candidate->format('F j, Y');
}
