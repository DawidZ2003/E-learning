<?php
session_start();

if (empty($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit();
}

$conn = mysqli_connect("127.0.0.1","dm81079_z14","Dawidek7003#","dm81079_z14");
mysqli_set_charset($conn,"utf8");

$idt = (int)$_GET['idt'];
$idp = $_SESSION['idp'];

// test info
$test = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM test WHERE idt=$idt")
);

// 🔥 TYLKO WYNIKI TEGO USERA
$result = mysqli_query($conn, "
    SELECT * FROM wyniki 
    WHERE idt=$idt 
    AND idp=$idp
    ORDER BY datetime DESC
");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Moje wyniki</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h2>📊 Moje wyniki testu: <?= $test['nazwa'] ?></h2>

<p>Zalogowany użytkownik: <b><?= $_SESSION['login'] ?></b></p>
<hr>

<?php
if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        echo "
        <div class='card p-3 mb-2'>
            <p><b>Data:</b> ".$row['datetime']."</p>
            <p><b>Punkty:</b> ".$row['punkty']."</p>

            <a href='".$row['plik_pdf']."' target='_blank' class='btn btn-success btn-sm'>
                📄 Otwórz PDF
            </a>
        </div>
        ";
    }

} else {
    echo "<p>Nie masz jeszcze wyników tego testu</p>";
}
?>

<a href="test.php" class="btn btn-secondary mt-3">⬅ Powrót</a>

</body>
</html>