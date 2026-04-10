<?php
declare(strict_types=1);
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (empty($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit();
}

$conn = mysqli_connect("127.0.0.1","dm81079_z14","Dawidek7003#","dm81079_z14");
mysqli_set_charset($conn,"utf8");

$idt = (int)$_GET['idt'];

// pobranie testu
$test = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM test WHERE idt=$idt"));

// pobranie pytań
$pytania = mysqli_query($conn, "SELECT * FROM pytania WHERE idt=$idt");

// jeśli formularz wysłany
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $punkty = 0;

    foreach ($_POST['odp'] as $idpyt => $odpowiedzi) {

        $q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pytania WHERE idpyt=$idpyt"));

        $poprawne = [];
        if ($q['a']) $poprawne[] = 'a';
        if ($q['b']) $poprawne[] = 'b';
        if ($q['c']) $poprawne[] = 'c';
        if ($q['d']) $poprawne[] = 'd';

        sort($poprawne);
        sort($odpowiedzi);

        if ($poprawne === $odpowiedzi) {
            $punkty++;
        }
    }

    $data = date('Y-m-d H:i:s');

    $idu = $_SESSION['idp'];
    
    $_SESSION['odpowiedzi'] = $_POST['odp']; // 🔥 TO JEST KLUCZ
    
    header("Location: generuj_pdf.php?idt=$idt&pkt=$punkty");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Test</title>

<style>
body {
    background-color: #f5f7fa;
    padding: 30px;
    font-family: Arial, sans-serif;
}

.test-container {
    max-width: 900px;
    margin: auto;
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.pytanie-box {
    margin-bottom: 25px;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #ddd;
    background-color: #fafafa;
}

.odpowiedz {
    display: block;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 8px;
    transition: 0.2s;
    cursor: pointer;
}

.odpowiedz:hover {
    background-color: #e2e8f0;
}

.odpowiedz input {
    margin-right: 10px;
}

.timer {
    font-size: 20px;
    font-weight: bold;
    color: #dc2626;
}

button {
    padding: 12px 25px;
    border: none;
    border-radius: 10px;
    background-color: #1e293b;
    color: white;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background-color: #334155;
}
</style>

<script>
// TIMER
let czas = <?php echo $test['max_time'] * 60; ?>;

function odliczanie() {
    let min = Math.floor(czas / 60);
    let sek = czas % 60;

    document.getElementById("timer").innerHTML = min + ":" + (sek<10?"0":"") + sek;

    czas--;

    if (czas < 0) {
        document.getElementById("form").submit();
    }
}

setInterval(odliczanie, 1000);
</script>

</head>

<body>

<div class="test-container">

<h2><?php echo $test['nazwa']; ?></h2>
<h3>Czas: <span id="timer" class="timer"></span></h3>

<form method="POST" id="form">

<?php
while ($row = mysqli_fetch_assoc($pytania)) {

    echo "<div class='pytanie-box'>";
    echo "<h4>".$row['tresc_pytania']."</h4>";

    if ($row['plik_multimedialny']) {
        echo "<img src='".$row['plik_multimedialny']."' style='max-width:300px; margin:10px 0;'>";
    }

    foreach (['a','b','c','d'] as $litera) {
        if ($row["odpowiedz_$litera"]) {

            echo "<label class='odpowiedz'>
                    <input type='checkbox' name='odp[".$row['idpyt']."][]' value='$litera'>
                    ".$row["odpowiedz_$litera"]."
                  </label>";
        }
    }

    echo "</div>";
}
?>

<button type="submit">✅ Zakończ test</button>

</form>

</div>

</body>
</html>