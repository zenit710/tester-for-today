<?php
date_default_timezone_set('Europe/Warsaw');

const DIR = __DIR__;

include DIR . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'PHPMailerAutoload.php';

const MAIL_SUBJECT = 'Tester na dziś';
const MESSAGE_PATERN = 'Dzisiaj (%s) zadania testuje: %s';
const MAIL_FROM = 'entertainment.tester@gmail.com';
const PASS = 'zaq1@WSX';

$confFile = DIR . DIRECTORY_SEPARATOR . 'tester.txt';
$teamFile = DIR . DIRECTORY_SEPARATOR . 'team.txt';

$subscribers = array(
    'k.malek@tvn.pl',
    's.bondek@tvn.pl',
    'd.bialy@tvn.pl',
    'm.morus@tvn.pl',
    'dawid_ziobro@tvn.pl',
    'Pawel_Mrowiec@tvn.pl'
);

try {
    $weekday = (int)date('w');

    if ($weekday == 0 || $weekday == 6) {
        logMessage("Dzien tygodnia: $weekday - dzisiaj nie zmieniamy testera");
        exit;
    }

    if (!file_exists($teamFile)) {
        logMessage("Byc moze plik ze skladem zespolu nie istenieje?");
    }

    $team = explode("\n", file_get_contents($teamFile));
    array_splice($team, -1, 1);
    $teamSize = count($team);

    $lastTesterPosition = file_exists($confFile) ? (int)file_get_contents($confFile) : $teamSize - 1;
    $lastTester = $team[$lastTesterPosition];

    logMessage("Ostatnio testowal: $lastTester");

    $newTesterPosition = $lastTesterPosition < $teamSize - 1 ? $lastTesterPosition + 1 : 0;
    $newTester = $team[$newTesterPosition];

    logMessage("Nowy tester: $newTester");

    $sendTo = implode(',', $subscribers);
    $message = sprintf(MESSAGE_PATERN, date('d-m-Y'), $newTester);

    sendMail($subscribers, $message);
    file_put_contents($confFile, $newTesterPosition);

    logMessage("Informacja o testerach wysłana do: " . implode(", ", $subscribers));
} catch (\Exception $e) {
    print_r($e);
}

function sendMail($subscribers, $message)
{
    $mail = new PHPMailer(true);

    //Server settings
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = MAIL_FROM;                 // SMTP username
    $mail->Password = PASS;                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to
    $mail->CharSet = 'UTF-8';

    //Recipients
    $mail->setFrom(MAIL_FROM, 'Entertainment Tester');

    foreach ($subscribers as $subscriber) {
        $mail->addBCC($subscriber);
    }

    $mail->Subject = MAIL_SUBJECT;
    $mail->Body = $message;
    $mail->send();
}

function logMessage($message)
{
    echo "\n" . date('Y-m-d H:i:s') . "\t" . $message;
}
