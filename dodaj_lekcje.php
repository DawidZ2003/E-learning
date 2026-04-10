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
if (isset($_POST['dodaj'])) {
    $nazwa = mysqli_real_escape_string($conn, $_POST['nazwa']);
    $tresc = mysqli_real_escape_string($conn, $_POST['tresc']);
    $idc = $_SESSION['idc'];

    $plik = '';
    if (!empty($_FILES['plik']['name'])) {
        $plik = basename($_FILES['plik']['name']);
        $tmp = $_FILES['plik']['tmp_name'];
        move_uploaded_file($tmp, $plik); // zapis w tym samym folderze
    }

    // Wstawienie lekcji z informacją o autorze
    $sql = "INSERT INTO lekcje (idc, nazwa, tresc, plik_multimedialny)
            VALUES ('$idc', '$nazwa', '$tresc', '$plik')";

    if (mysqli_query($conn, $sql)) {
        $msg = "✅ Lekcja została dodana!";
    } else {
        $msg = "❌ Błąd podczas dodawania lekcji!";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>Dodaj Lekcję - E-learning</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- CKEditor CDN -->
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<style>
body { background-color: #f5f7fa; }
.container { max-width: 700px; margin: 50px auto; background: white; padding: 20px; border-radius: 15px; }
</style>
</head>
<body>

<div class="container">
    <h3>➕ Dodaj nową lekcję</h3>
    <?php if($msg) echo "<div class='alert alert-info'>$msg</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-2">
            <label>Nazwa lekcji</label>
            <input type="text" name="nazwa" class="form-control" required>
        </div>

        <div class="mb-2">
            <label>Treść lekcji</label>
            <textarea name="tresc" class="form-control" rows="10" required></textarea>
        </div>

        <div class="mb-3">
            <label>Plik multimedialny (jpg, png, mp3, mp4)</label>
            <input type="file" name="plik" class="form-control" required>
        </div>

        <button type="submit" name="dodaj" class="btn btn-success">➕ Dodaj lekcję</button>
        <a href="pracownik.php" class="btn btn-secondary">⬅ Powrót</a>
    </form>
</div>

<script>
    CKEDITOR.replace('tresc'); // inicjalizacja CKEditor
</script>

</body>
</html>