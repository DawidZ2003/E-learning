<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('tcpdf/tcpdf.php');

// DB
$conn = mysqli_connect("127.0.0.1","dm81079_z14","Dawidek7003#","dm81079_z14");
mysqli_set_charset($conn,"utf8");

if (!$conn) {
    die("Błąd połączenia z bazą");
}

// dane
$idp = $_SESSION['idp'] ?? 0;
$idt = (int)$_GET['idt'];
$punkty = (int)$_GET['pkt'];
$data = date('Y-m-d H:i:s');

// test info
$test = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT test.*, coach.login AS coach_login
        FROM test
        JOIN coach ON test.idc = coach.idc
        WHERE test.idt = $idt
    ")
);
// odpowiedzi usera
$odpowiedziUser = $_SESSION['odpowiedzi'] ?? [];
unset($_SESSION['odpowiedzi']);

// folder PDF
$dir = __DIR__ . "/pdf/";

if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

// plik
$filename = "test_" . $idt . "_" . time() . ".pdf";
$filepath = $dir . $filename;
$fileurl  = "pdf/" . $filename;

// PDF INIT
$pdf = new TCPDF();
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// 🔥 NAGŁÓWEK
$pdf->SetFont('dejavusans','B',18);
$pdf->Cell(0, 12, "WYNIK TESTU", 0, 1, 'C');

$pdf->SetFont('dejavusans','',10);
$pdf->Ln(2);

// 🔥 INFO O TEŚCIE
$pdf->Cell(0, 6, "Nazwa testu: " . ($test['nazwa'] ?? 'brak'), 0, 1);
$pdf->Cell(0, 6, "Autor testu: " . ($test['coach_login'] ?? 'brak'), 0, 1);
$pdf->Cell(0, 6, "Zdający: " . ($_SESSION['login'] ?? 'brak loginu'), 0, 1);
$pdf->Cell(0, 6, "Data zakończenia: " . $data, 0, 1);
$pdf->Cell(0, 6, "Punkty: " . $punkty, 0, 1);

$pdf->Ln(5);

// 🔥 PYTANIA
$pytania = mysqli_query($conn, "SELECT * FROM pytania WHERE idt=$idt");

$i = 1;

while ($q = mysqli_fetch_assoc($pytania)) {

    // pytanie
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('dejavusans','B',10);

    $pdf->MultiCell(0, 8, "Pytanie $i: " . $q['tresc_pytania'], 0, 'L');
    $pdf->Ln(1);

    // odpowiedzi
    $pdf->SetFont('dejavusans','',10);

    foreach(['a','b','c','d'] as $l){

        $poprawne = !empty($q[$l]);

        $zaznaczone = !empty($odpowiedziUser[$q['idpyt']]) &&
                      in_array($l, $odpowiedziUser[$q['idpyt']]);

        // checkbox
        $checkbox = $zaznaczone ? "☒" : "☐";

        // kolory
        if ($poprawne && $zaznaczone) {
            $pdf->SetTextColor(0, 150, 0);
        }
        elseif (!$poprawne && $zaznaczone) {
            $pdf->SetTextColor(200, 0, 0);
        }
        else {
            $pdf->SetTextColor(0, 0, 0);
        }

        $pdf->MultiCell(0, 6, $checkbox . " " . $q["odpowiedz_$l"], 0, 'L');
    }

    $pdf->Ln(3);

    // separator
    $pdf->SetDrawColor(200,200,200);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(5);

    $i++;
}

// zapis PDF
$pdf->Output($filepath, 'F');

// zapis do bazy
mysqli_query($conn, "
    INSERT INTO wyniki (idp, idt, datetime, punkty, plik_pdf)
    VALUES ($idp, $idt, '$data', $punkty, '$fileurl')
");

// redirect
header("Location: $fileurl");
exit();