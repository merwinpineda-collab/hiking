<?php
// mailer.php — SMTP email helper (no external library required)
// Uses PHP's built-in mail() with SMTP via stream context, OR swap to PHPMailer if installed.

/**
 * Send a booking-confirmation email.
 *
 * Configuration: edit the constants below to match your SMTP provider.
 * Works with Gmail (App Password), Brevo (Sendinblue), Mailgun, etc.
 *
 * @param string $to_email   Recipient email
 * @param string $to_name    Recipient display name
 * @param int    $reserve_id Reservation ID shown in the email
 * @param string $date       Booking date string
 * @param string $time       Booking time string
 * @return bool  true on success, false on failure
 */
function sendBookingConfirmationEmail(
    string $to_email,
    string $to_name,
    int    $reserve_id,
    string $date,
    string $time
): bool {

    // ── SMTP CONFIG ─────────────────────────────────────────────────────────
    // Change these to your real SMTP credentials.
    // For Gmail: enable 2FA → generate an App Password → paste below.
    define('SMTP_HOST',     'smtp.gmail.com');
    define('SMTP_PORT',     587);                // 587 = STARTTLS  |  465 = SSL
    define('SMTP_USER',     'your_email@gmail.com');   // ← change
    define('SMTP_PASS',     'your_app_password');       // ← change
    define('SMTP_FROM',     'your_email@gmail.com');
    define('SMTP_FROM_NAME','WilderPath Reservations');
    // ────────────────────────────────────────────────────────────────────────

    // If PHPMailer is available (installed via Composer), use it.
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return _sendViaPHPMailer($to_email, $to_name, $reserve_id, $date, $time);
    }

    // Fallback: native PHP socket SMTP (works for most shared hosts)
    return _sendViaSocket($to_email, $to_name, $reserve_id, $date, $time);
}

/* ── PHPMailer path ─────────────────────────────────────────────────────── */
function _sendViaPHPMailer(string $to, string $name, int $id, string $date, string $time): bool {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Your WilderPath Booking is Confirmed!';
        $mail->Body    = _emailBody($name, $id, $date, $time);
        $mail->AltBody = _emailBodyPlain($name, $id, $date, $time);
        $mail->send();
        return true;
    } catch (\Exception $e) {
        error_log('PHPMailer error: ' . $e->getMessage());
        return false;
    }
}

/* ── Native socket SMTP (no library) ────────────────────────────────────── */
function _sendViaSocket(string $to, string $name, int $id, string $date, string $time): bool {
    $subject  = '=?UTF-8?B?' . base64_encode('Your WilderPath Booking is Confirmed!') . '?=';
    $htmlBody = _emailBody($name, $id, $date, $time);
    $boundary = md5(uniqid());

    $headers  = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">\r\n";
    $headers .= "To: {$name} <{$to}>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";

    $body  = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
    $body .= _emailBodyPlain($name, $id, $date, $time) . "\r\n";
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
    $body .= $htmlBody . "\r\n";
    $body .= "--{$boundary}--";

    // Try to open SMTP socket with STARTTLS
    $errno = $errstr = '';
    $ctx = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
    $sock = @stream_socket_client(
        "tcp://" . SMTP_HOST . ":" . SMTP_PORT,
        $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx
    );
    if (!$sock) {
        error_log("SMTP connect failed: {$errno} {$errstr}");
        return false;
    }

    $read = function() use ($sock) { return fgets($sock, 512); };
    $write = function(string $cmd) use ($sock) { fwrite($sock, $cmd . "\r\n"); };

    $read(); // 220 greeting
    $write("EHLO " . gethostname());
    while ($line = $read()) { if (substr($line,3,1) === ' ') break; }
    $write("STARTTLS");
    $read(); // 220
    stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    $write("EHLO " . gethostname());
    while ($line = $read()) { if (substr($line,3,1) === ' ') break; }
    $write("AUTH LOGIN");
    $read(); // 334
    $write(base64_encode(SMTP_USER));
    $read(); // 334
    $write(base64_encode(SMTP_PASS));
    $r = $read(); // 235
    if (substr($r, 0, 3) !== '235') { fclose($sock); error_log("SMTP auth failed: $r"); return false; }

    $write("MAIL FROM:<" . SMTP_FROM . ">");
    $read();
    $write("RCPT TO:<{$to}>");
    $read();
    $write("DATA");
    $read(); // 354
    $write($headers . "\r\n" . $body . "\r\n.");
    $r = $read(); // 250
    $write("QUIT");
    fclose($sock);

    return substr($r, 0, 3) === '250';
}

/* ── Email templates ────────────────────────────────────────────────────── */
function _emailBody(string $name, int $id, string $date, string $time): string {
    $safeDate = htmlspecialchars($date);
    $safeTime = htmlspecialchars($time);
    $safeName = htmlspecialchars($name);
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { margin:0; padding:0; background:#f0f4f0; font-family:'Segoe UI',sans-serif; }
    .wrap { max-width:600px; margin:30px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.1); }
    .header { background:linear-gradient(135deg,#2e7d32,#43a047); padding:36px 32px; text-align:center; }
    .header h1 { color:#fff; margin:0; font-size:26px; letter-spacing:.5px; }
    .header p  { color:#c8e6c9; margin:6px 0 0; font-size:14px; }
    .body  { padding:32px 36px; }
    .body h2 { color:#1b5e20; font-size:20px; margin:0 0 12px; }
    .body p  { color:#444; font-size:15px; line-height:1.7; }
    .detail-box { background:#f1f8f1; border:1px solid #a5d6a7; border-radius:12px; padding:20px 24px; margin:20px 0; }
    .detail-box table { width:100%; border-collapse:collapse; }
    .detail-box td { padding:7px 0; font-size:14px; color:#333; }
    .detail-box td:first-child { font-weight:700; color:#2e7d32; width:120px; }
    .cta { text-align:center; margin:28px 0 8px; }
    .cta a { background:#2e7d32; color:#fff; text-decoration:none; padding:13px 34px; border-radius:50px; font-size:15px; font-weight:600; display:inline-block; }
    .footer { background:#f5f5f5; padding:18px; text-align:center; font-size:12px; color:#888; border-top:1px solid #e0e0e0; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <h1>🌿 WilderPath</h1>
      <p>Booking Confirmation</p>
    </div>
    <div class="body">
      <h2>Hi, {$safeName}!</h2>
      <p>Great news — your hiking reservation has been <strong>confirmed</strong>. We can't wait to see you on the trail!</p>
      <div class="detail-box">
        <table>
          <tr><td>Booking ID</td><td>#REF{$id}</td></tr>
          <tr><td>Date</td><td>{$safeDate}</td></tr>
          <tr><td>Start Time</td><td>{$safeTime}</td></tr>
          <tr><td>Status</td><td>✅ Confirmed</td></tr>
        </table>
      </div>
      <p>Please arrive at the trailhead 15 minutes before your scheduled start time. Wear proper footwear, carry water, and let someone know your plans.</p>
      <p>Questions? Reply to this email or call us at <strong>+63 912 345 6789</strong>.</p>
      <div class="cta">
        <a href="#">View My Booking</a>
      </div>
    </div>
    <div class="footer">© 2025 WilderPath · Built for Hikers · Stay Safe</div>
  </div>
</body>
</html>
HTML;
}

function _emailBodyPlain(string $name, int $id, string $date, string $time): string {
    return "Hi {$name},\n\nYour WilderPath hiking reservation (#REF{$id}) has been confirmed!\n\nDate: {$date}\nTime: {$time}\nStatus: Confirmed\n\nPlease arrive 15 minutes early. Bring water and proper footwear.\n\nSee you on the trail!\n— WilderPath Team";
}
?>