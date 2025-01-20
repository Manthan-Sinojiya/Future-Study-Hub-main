<?php
session_start();
header('Content-Type: application/json');
require 'vendor/autoload.php';

use Razorpay\Api\Api;

// Razorpay credentials
$api_key = 'rzp_test_zP5TXgpxedUZsA';
$api_secret = 'bDGORQSucNPcR4kFxtVt7bYO';
$api = new Api($api_key, $api_secret);

$payment_id = $_POST['razorpay_payment_id'];
$order_id = $_POST['razorpay_order_id'];
$signature = $_POST['razorpay_signature'];
$course_id = $_POST['course_id'];

// Verify payment signature
$expected_signature = hash_hmac('sha256', $order_id . '|' . $payment_id, $api_secret);
if ($signature === $expected_signature) {
    $student_id = $_SESSION['student_id'];

    try {
        $payment = $api->payment->fetch($payment_id);
        $razorpay_method = $payment['method'];
        $method_mapping = [
            'card' => 1,
            'upi' => 4,
            'netbanking' => 3,
            'wallet' => 5,
            'emi' => 6
        ];
        $method_id = $method_mapping[$razorpay_method] ?? 0;

        include('./assets/include/db.php');
        
        $conn->begin_transaction();
        $stmt = $conn->prepare("SELECT price FROM courses WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $course = $stmt->get_result()->fetch_assoc();
        $amount = $course['price'];
        $stmt->close();

        $payment_date = date('Y-m-d H:i:s');
        $status = "Paid";
        $stmt = $conn->prepare("INSERT INTO payment (student_id, course_id, amount, payment_date, status, method_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iidssi", $student_id, $course_id, $amount, $payment_date, $status, $method_id);
        $stmt->execute();
        $payment_id_db = $conn->insert_id;
        $stmt->close();
        
        $transaction_date = date('Y-m-d H:i:s');
        $transaction_status = "Completed";
        $transaction_reference = $payment_id_db;
        $stmt = $conn->prepare("INSERT INTO transactions (student_id, payment_id, transaction_date, amount, status, transaction_reference) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdss", $student_id, $payment_id_db, $transaction_date, $amount, $transaction_status, $transaction_reference);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE invoices SET status = ? WHERE student_id = ? AND course_id = ? AND status = 'unpaid'");
        $stmt->bind_param("sii", $status, $student_id, $course_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => "Failed to process the payment: " . $e->getMessage()]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => "Payment verification failed due to signature mismatch."
    ]);
}
?>
