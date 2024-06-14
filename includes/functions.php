<?php
require '../vendor/autoload.php';
use Twilio\Rest\Client;

function send_sms_verification($phone_number, $code, $connection) {
    if (!preg_match('/^\+60/', $phone_number)) {
        $phone_number = '+60' . ltrim($phone_number, '0');
    }

    $sid = 'AC305436d99342d9ec1668d8f95db78ad0';
    $token = 'ec8efa430ad1ec6cb28e7cc9f66da7d6';
    $twilio = new Client($sid, $token);

    try {
        $verification = $twilio->verify->v2->services("VA88ed72076202532da313fa131d322660")
                                           ->verifications
                                           ->create($phone_number, "sms");

        if ($verification->status === 'pending') {
            $stmt = $connection->prepare("INSERT INTO sms_verifications (phone_number, code) VALUES (?, ?)");
            $stmt->bind_param("ss", $phone_number, $code);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Failed to send verification SMS.");
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}

function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function validate_matric_number($matric_number) {
    return preg_match('/^[a-zA-Z0-9]{7,10}$/', $matric_number);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone_number($phone_number) {
    return preg_match('/^01[0-9]-[0-9]{7,8}$/', $phone_number);
}

function upload_profile_picture($file, $target_dir) {
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($file["tmp_name"]);

    if ($check === false) {
        return "File is not an image.";
    }

    if (file_exists($target_file)) {
        return "Sorry, file already exists.";
    }

    if ($file["size"] > 500000) {
        return "Sorry, your file is too large.";
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        return "Sorry, only JPG, JPEG, and PNG files are allowed.";
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return "The file " . htmlspecialchars(basename($file["name"])) . " has been uploaded.";
    } else {
        return "Sorry, there was an error uploading your file.";
    }
}
?>
