<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$firstName = htmlspecialchars($data["firstName"] ?? "");
$lastName = htmlspecialchars($data["lastName"] ?? "");
$email = htmlspecialchars($data["email"] ?? "");
$phone = htmlspecialchars($data["phone"] ?? "");
$location = htmlspecialchars($data["location"] ?? "");
$message = htmlspecialchars($data["message"] ?? "");

$to = "daniel@baumschrat.at";
$subject = "Neue Kontaktanfrage von Baumschrat-Website";

$body = "Sie haben eine neue Kontaktanfrage von Ihrer Website erhalten:\n\n";
$body .= "Vorname: " . $firstName . "\n";
$body .= "Nachname: " . $lastName . "\n";
$body .= "E-Mail: " . $email . "\n";
$body .= "Telefon: " . $phone . "\n";
$body .= "Ort der Arbeiten: " . $location . "\n";
$body .= "Nachricht:\n" . $message . "\n";

$headers = "From: webmaster@baumschrat.at\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(["success" => true, "message" => "Nachricht erfolgreich gesendet!"]);
} else {
    echo json_encode(["success" => false, "message" => "Fehler beim Senden der Nachricht."]);
}

?>

