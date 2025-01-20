<?php
session_start();
header('Content-Type: application/json');
require 'vendor/autoload.php';
use Razorpay\Api\Api;

include('./assets/include/db.php');

// Check if user session has `student_id`
if (!isset($_SESSION['student_id'])) {
    echo json_encode(['error' => 'Unauthorized access. Please log in.']);
    exit;
}

$student_id = $_SESSION['student_id'];
$course_id = $_POST['course_id'] ?? 0;

if ($course_id > 0) {
    try {
        $sql = "SELECT * FROM courses WHERE course_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();

        if ($course) {
            $invoice_date = date('Y-m-d H:i:s');
            $due_date = date('Y-m-d H:i:s', strtotime('+7 days'));
            $total_amount = $course['price'];
            $status = 'unpaid';

            // Insert invoice details
            $sql_invoice = "INSERT INTO invoices (student_id, course_id, invoice_date, due_date, total_amount, status)
                            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_invoice = $conn->prepare($sql_invoice);
            $stmt_invoice->bind_param("iissds", $student_id, $course_id, $invoice_date, $due_date, $total_amount, $status);
            $stmt_invoice->execute();
            $invoice_id = $stmt_invoice->insert_id;
            $stmt_invoice->close();

            $api_key = 'rzp_test_zP5TXgpxedUZsA';
            $api_secret = 'bDGORQSucNPcR4kFxtVt7bYO';
            $api = new Api($api_key, $api_secret);

            $orderData = [
                'receipt' => 'rcptid_' . $invoice_id,
                'amount' => $total_amount * 100,
                'currency' => 'INR',
                'payment_capture' => 1
            ];

            $razorpayOrder = $api->order->create($orderData);
            $order_id = $razorpayOrder['id'];

            $response = [
                'api_key' => $api_key,
                'amount' => $orderData['amount'],
                'currency' => $orderData['currency'],
                'course_name' => $course['course_name'],
                'course_description' => $course['description'],
                'order_id' => $order_id,
                'invoice_id' => $invoice_id
            ];
            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Course not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid course ID']);
}
?>
