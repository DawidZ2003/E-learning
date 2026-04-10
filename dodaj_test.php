<?php
declare(strict_types=1);
session_start();

// SPRAWDZENIE LOGOWANIA I ROLI
if (!isset($_SESSION['loggedin']) || $_SESSION['rola'] !== 'coach') {
    header('Location: logowanie.php');
    exit();
}

// POŁĄCZENIE Z BAZĄ
$conn = mysqli_connect("127.0.0.1", "dm81079_z14", "Dawidek7003#", "dm81079_z14");
if (!$conn) die("Błąd połączenia: " . mysqli_connect_error());
mysqli_set_charset($conn, "utf8");

// OBSŁUGA FORMULARZA
$msg = '';
if (isset($_POST['dodaj_test'])) {
    $idc = $_SESSION['idc'];
    $nazwa = mysqli_real_escape_string($conn, $_POST['nazwa']);
    $max_time = (int)$_POST['max_time'];

    // Dodanie testu
    $sql_test = "INSERT INTO test (idc, nazwa, max_time) VALUES ('$idc', '$nazwa', '$max_time')";
    if (mysqli_query($conn, $sql_test)) {
        $idt = mysqli_insert_id($conn); // pobranie ID nowego testu

        // Dodanie pytań (minimum 3)
        for ($i=0; $i < count($_POST['tresc_pytania']); $i++) {
            $tresc = mysqli_real_escape_string($conn, $_POST['tresc_pytania'][$i]);
            $a = mysqli_real_escape_string($conn, $_POST['odpowiedz_a'][$i]);
            $b = mysqli_real_escape_string($conn, $_POST['odpowiedz_b'][$i]);
            $c = mysqli_real_escape_string($conn, $_POST['odpowiedz_c'][$i]);
            $d = mysqli_real_escape_string($conn, $_POST['odpowiedz_d'][$i]);
            $fa = isset($_POST['a'][$i]) ? 1 : 0;
            $fb = isset($_POST['b'][$i]) ? 1 : 0;
            $fc = isset($_POST['c'][$i]) ? 1 : 0;
            $fd = isset($_POST['d'][$i]) ? 1 : 0;

            $plik = '';
            if (!empty($_FILES['plik']['name'][$i])) {
                $plik = basename($_FILES['plik']['name'][$i]);
                $tmp = $_FILES['plik']['tmp_name'][$i];
                move_uploaded_file($tmp, $plik);
            }

            $sql_pytanie = "INSERT INTO pytania 
                (idt, tresc_pytania, odpowiedz_a, odpowiedz_b, odpowiedz_c, odpowiedz_d, a, b, c, d, plik_multimedialny)
                VALUES ('$idt', '$tresc', '$a', '$b', '$c', '$d', '$fa', '$fb', '$fc', '$fd', '$plik')";

            mysqli_query($conn, $sql_pytanie);
        }

        $msg = "✅ Test został dodany!";
    } else {
        $msg = "❌ Błąd podczas dodawania testu!";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>Dodaj Test - E-learning</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f5f7fa; }
.container { max-width: 900px; margin: 50px auto; background: white; padding: 20px; border-radius: 15px; }
</style>
</head>
<body>

<div class="container">
    <h3>➕ Dodaj nowy test</h3>
    <?php if($msg) echo "<div class='alert alert-info'>$msg</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-2">
            <label>Nazwa testu</label>
            <input type="text" name="nazwa" class="form-control" required>
        </div>

        <div class="mb-2">
            <label>Maksymalny czas (minuty)</label>
            <input type="number" name="max_time" class="form-control" min="1" required>
        </div>

        <hr>
        <h5>Pytania (minimum 3)</h5>

        <?php for($i=0; $i<3; $i++): ?>
        <div class="mb-3 border p-3 rounded">
            <label>Pytanie <?= $i+1 ?></label>
            <textarea name="tresc_pytania[]" class="form-control" rows="3" required></textarea>

            <div class="mt-2">
                <label>Odpowiedzi:</label>
                <input type="text" name="odpowiedz_a[]" placeholder="A" class="form-control mt-1" required>
                <input type="text" name="odpowiedz_b[]" placeholder="B" class="form-control mt-1" required>
                <input type="text" name="odpowiedz_c[]" placeholder="C" class="form-control mt-1" required>
                <input type="text" name="odpowiedz_d[]" placeholder="D" class="form-control mt-1" required>
            </div>

            <div class="form-check mt-2">
                <input type="checkbox" name="a[<?= $i ?>]" class="form-check-input" id="a<?= $i ?>">
                <label class="form-check-label" for="a<?= $i ?>">Poprawna A</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="b[<?= $i ?>]" class="form-check-input" id="b<?= $i ?>">
                <label class="form-check-label" for="b<?= $i ?>">Poprawna B</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="c[<?= $i ?>]" class="form-check-input" id="c<?= $i ?>">
                <label class="form-check-label" for="c<?= $i ?>">Poprawna C</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="d[<?= $i ?>]" class="form-check-input" id="d<?= $i ?>">
                <label class="form-check-label" for="d<?= $i ?>">Poprawna D</label>
            </div>

            <div class="mt-2">
                <label>Plik multimedialny</label>
                <input type="file" name="plik[]" class="form-control" required>
            </div>
        </div>
        <?php endfor; ?>

        <button type="submit" name="dodaj_test" class="btn btn-success">➕ Dodaj test</button>
        <a href="pracownik.php" class="btn btn-secondary">⬅ Powrót</a>
    </form>
</div>

</body>
</html>