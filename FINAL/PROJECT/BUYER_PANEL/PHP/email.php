<?php


if (!function_exists('send_order_receipt')) {

    function send_order_receipt(array $smtp, string $toEmail, string $toName, array $order): array {
        $subject = 'Your Order Receipt #' . ($order['id'] ?? '');
        $boundary = 'bnd_' . bin2hex(random_bytes(8));
        $date = date('r');

        $itemsHtml = '';
        foreach ($order['items'] as $it) {
            $lineTotal = number_format($it['price'] * $it['quantity'], 2);
            $itemsHtml .= '<tr><td style="padding:4px 8px;border:1px solid #ddd;">' . 
                htmlspecialchars($it['name']) . '</td><td style="padding:4px 8px;border:1px solid #ddd;">' .
                intval($it['quantity']) . '</td><td style="padding:4px 8px;border:1px solid #ddd;">$' .
                number_format($it['price'],2) . '</td><td style="padding:4px 8px;border:1px solid #ddd;">$' . $lineTotal . '</td></tr>';
        }

        $billing = $order['billing'] ?? [];
        $billHtml = '';
        foreach ($billing as $k=>$v) {
            if ($v === '') continue;
            $billHtml .= '<div><strong>' . htmlspecialchars(ucwords(str_replace('_',' ',$k))) . ':</strong> ' . htmlspecialchars($v) . '</div>';
        }

        $totalFormatted = number_format($order['total'] ?? 0, 2);
        $payLine = '';
        if (!empty($order['payment_method'])) {
            $pm = strtolower($order['payment_method']);
            $pmLabel = ($pm === 'cod') ? 'Cash On Delivery' : htmlspecialchars($order['payment_method']);
            $payLine = '<div style="margin-top:8px;"><strong>Payment Method:</strong> ' . $pmLabel . '</div>';
        }
        $htmlBody = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;font-size:14px;color:#111;">'
            . '<h2 style="margin:0 0 12px;">Thank you for your order!</h2>'
            . '<p>Order <strong>#' . htmlspecialchars($order['id']) . '</strong> has been placed.</p>'
            . $payLine
            . '<h3 style="margin:16px 0 8px;">Items</h3>'
            . '<table style="border-collapse:collapse;border:1px solid #ddd;min-width:400px;">'
            . '<thead><tr style="background:#f5f5f5;">'
            . '<th style="padding:6px 8px;border:1px solid #ddd;text-align:left;">Product</th>'
            . '<th style="padding:6px 8px;border:1px solid #ddd;text-align:left;">Qty</th>'
            . '<th style="padding:6px 8px;border:1px solid #ddd;text-align:left;">Price</th>'
            . '<th style="padding:6px 8px;border:1px solid #ddd;text-align:left;">Total</th>'
            . '</tr></thead><tbody>' . $itemsHtml . '</tbody></table>'
            . '<h3 style="margin:16px 0 8px;">Billing</h3>' . $billHtml
            . '<p style="margin-top:16px;font-size:15px;"><strong>Grand Total: $' . $totalFormatted . '</strong></p>'
            . '<p style="margin-top:24px;color:#555;">This is an automated email; please do not reply.</p>'
            . '</body></html>';

        $headers = [
            'Date: ' . $date,
            'From: ' . ($smtp['from_name'] ?? 'Store') . ' <' . $smtp['from_email'] . '>',
            'To: ' . $toName . ' <' . $toEmail . '>',
            'Subject: ' . $subject,
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8'
        ];
        $data = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody . "\r\n";

        return smtp_raw_send($smtp, $toEmail, $data);
    }
}

// Simple OTP email sender for login verification
if (!function_exists('send_login_otp')) {
    function send_login_otp(array $smtp, string $toEmail, string $toName, string $otp): array {
        $subject = 'Your Login OTP Code';
        $date = date('r');
        $body = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;font-size:14px;color:#111;">'
            .'<h2 style="margin:0 0 12px;">Login Verification</h2>'
            .'<p>Your One-Time Password (OTP) is:</p>'
            .'<div style="font-size:24px;letter-spacing:4px;font-weight:bold;background:#f5f5f5;padding:12px 16px;display:inline-block;border:1px solid #ddd;">'
            . htmlspecialchars($otp) . '</div>'
            .'<p style="margin-top:16px;">It expires in 5 minutes. If you did not request this, you can ignore the email.</p>'
            .'<p style="margin-top:24px;color:#555;">This is an automated email; do not reply.</p>'
            .'</body></html>';

        $headers = [
            'Date: ' . $date,
            'From: ' . ($smtp['from_name'] ?? 'Store') . ' <' . $smtp['from_email'] . '>',
            'To: ' . $toName . ' <' . $toEmail . '>',
            'Subject: ' . $subject,
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8'
        ];
        $data = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n";
        return smtp_raw_send($smtp, $toEmail, $data);
    }
}

// Password reset email sender
if (!function_exists('send_password_reset_email')) {
    function send_password_reset_email(array $smtp, string $toEmail, string $toName, string $resetLink, int $minutesValid = 30): array {
        $subject = 'Password Reset Request';
        $date = date('r');
        $body = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;font-size:14px;color:#111;">'
            .'<h2 style="margin:0 0 12px;">Reset Your Password</h2>'
            .'<p>We received a request to reset the password for your account. Click the button below to set a new password.</p>'
            .'<p style="margin:16px 0;"><a href="'.htmlspecialchars($resetLink).'" style="background:#2563eb;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none;font-weight:600;display:inline-block;">Reset Password</a></p>'
            .'<p>If the button does not work, copy and paste this URL into your browser:</p>'
            .'<p style="word-break:break-all;color:#1f2937;">'.htmlspecialchars($resetLink).'</p>'
            .'<p>This link will expire in '.intval($minutesValid).' minutes or after it is used.</p>'
            .'<p style="margin-top:24px;color:#555;">If you did not request this, you can safely ignore this email.</p>'
            .'</body></html>';
        $headers = [
            'Date: ' . $date,
            'From: ' . ($smtp['from_name'] ?? 'Store') . ' <' . $smtp['from_email'] . '>',
            'To: ' . $toName . ' <' . $toEmail . '>',
            'Subject: ' . $subject,
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8'
        ];
        $data = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n";
        return smtp_raw_send($smtp, $toEmail, $data);
    }
}

if (!function_exists('smtp_raw_send')) {

    function smtp_raw_send(array $smtp, string $to, string $data): array {
        $host = $smtp['host'];
        $port = (int)$smtp['port'];
        $timeout = 15;
        $secure = strtolower($smtp['secure'] ?? '');

        $remote = ($secure === 'ssl') ? 'ssl://' . $host : $host;
        $fp = @fsockopen($remote, $port, $errno, $errstr, $timeout);
        if (!$fp) {
            return ['ok'=>false,'error'=>'Connect failed: ' . $errstr];
        }
        $read = function() use ($fp) {
            $lines = '';
            while ($line = fgets($fp, 515)) {
                $lines .= $line;
                if (preg_match('/^\d{3} /', $line)) break; 
            }
            return $lines;
        };
        $expect = function($code) use ($read) {
            $resp = $read();
            if (substr($resp,0,3) != (string)$code) {
                return $resp; 
            }
            return true;
        };

        if (($r = $expect(220)) !== true) return ['ok'=>false,'error'=>'Banner: '.$r];
        fwrite($fp, "EHLO localhost\r\n");
        if (($r = $expect(250)) !== true) {
            fwrite($fp, "HELO localhost\r\n");
            if (($r = $expect(250)) !== true) return ['ok'=>false,'error'=>'HELO failed: '.$r];
        }

        if ($secure === 'tls') {
            fwrite($fp, "STARTTLS\r\n");
            if (($r = $expect(220)) !== true) return ['ok'=>false,'error'=>'STARTTLS failed: '.$r];
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                return ['ok'=>false,'error'=>'TLS negotiation failed'];
            }
            // Re-EHLO after TLS
            fwrite($fp, "EHLO localhost\r\n");
            if (($r = $expect(250)) !== true) return ['ok'=>false,'error'=>'EHLO (after TLS) failed: '.$r];
        }

        if (!empty($smtp['username'])) {
            fwrite($fp, "AUTH LOGIN\r\n");
            if (($r = $expect(334)) !== true) return ['ok'=>false,'error'=>'AUTH LOGIN not accepted: '.$r];
            fwrite($fp, base64_encode($smtp['username'])."\r\n");
            if (($r = $expect(334)) !== true) return ['ok'=>false,'error'=>'Username rejected: '.$r];
            fwrite($fp, base64_encode($smtp['password'])."\r\n");
            if (($r = $expect(235)) !== true) return ['ok'=>false,'error'=>'Password rejected: '.$r];
        }

        $from = $smtp['from_email'];
        fwrite($fp, "MAIL FROM:<$from>\r\n");
        if (($r = $expect(250)) !== true) return ['ok'=>false,'error'=>'MAIL FROM failed: '.$r];
        fwrite($fp, "RCPT TO:<$to>\r\n");
        if (($r = $expect(250)) !== true) return ['ok'=>false,'error'=>'RCPT TO failed: '.$r];
        fwrite($fp, "DATA\r\n");
        if (($r = $expect(354)) !== true) return ['ok'=>false,'error'=>'DATA not accepted: '.$r];

        $safeData = preg_replace('/\r?\n\./', "\r\n..", $data);
        fwrite($fp, $safeData."\r\n.\r\n");
        if (($r = $expect(250)) !== true) return ['ok'=>false,'error'=>'Message rejected: '.$r];

        fwrite($fp, "QUIT\r\n");
        fclose($fp);
        return ['ok'=>true,'error'=>''];
    }
}
