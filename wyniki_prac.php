<?php
session_start();

if (empty($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit();
}

$conn = mysqli_connect("127.0.0.1","dm81079_z14","Dawidek7003#","dm81079_z14");
mysqli_set_charset($conn,"utf8");

// 🔥 wszystkie wyniki + login usera + test
$query = "
SELECT 
    w.*,
    t.nazwa AS test_nazwa,
    u.login AS user_login
FROM wyniki w
JOIN test t ON w.idt = t.idt
JOIN pracownik u ON w.idp = u.idp
ORDER BY w.datetime DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Wyniki pracowników</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="container mt-4">

<h2>📊 Wyniki wszystkich pracowników</h2>

<hr>

<?php
if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        echo "
        <div class='card p-3 mb-3'>
            <h5>".$row['test_nazwa']."</h5>

            <p><b>Pracownik:</b> ".$row['user_login']."</p>
            <p><b>Data:</b> ".$row['datetime']."</p>
            <p><b>Punkty:</b> ".$row['punkty']."</p>

            <a class='btn btn-success btn-sm' target='_blank' href='".$row['plik_pdf']."'>
                📄 Otwórz PDF
            </a>
        </div>
        ";
    }

} else {
    echo "<p>Brak wyników</p>";
}
?>

<a href="pracownik.php" class="btn btn-secondary mt-3">⬅ Powrót</a>

</body>
</html>