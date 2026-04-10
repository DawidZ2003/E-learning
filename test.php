<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit();
}

// POŁĄCZENIE Z BAZĄ
$host = "127.0.0.1";
$user = "dm81079_z14";
$password = "Dawidek7003#";
$dbname = "dm81079_z14";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

// POBRANIE TESTÓW
$query = "SELECT t.*, c.login AS autor 
          FROM test t
          LEFT JOIN coach c ON t.idc = c.idc
          ORDER BY t.idt ASC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Wybór testu</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-color: #f5f7fa;
}

.test-box {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 15px;
    border: 2px solid #1e293b;
    transition: 0.2s;
}

.test-box:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

a {
    text-decoration: none;
    color: inherit;
}
</style>
</head>

<body>

<div class="container mt-4">

    <h2>📋 Wybierz test</h2>
    <p>Zalogowany jako: <b><?php echo $_SESSION['login']; ?></b></p>

    <hr>

    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
    ?>

        <div class="test-box">

            <h5><?= $row['nazwa'] ?></h5>

            <p><b>Autor:</b> <?= $row['autor'] ?></p>
            <p><b>Maksymalny czas:</b> <?= $row['max_time'] ?> min</p>

            <div class="mt-2">

                <!-- START TESTU -->
                <a href="rozpocznij_test.php?idt=<?= $row['idt'] ?>" class="btn btn-success btn-sm">
                    ▶ Rozpocznij
                </a>

                <!-- WYNIKI USERA -->
                <a href="wyniki_testu.php?idt=<?= $row['idt'] ?>" class="btn btn-primary btn-sm">
                    📊 Moje wyniki
                </a>

            </div>

        </div>

    <?php
        }
    } else {
        echo "<p>Brak dostępnych testów</p>";
    }
    ?>

    <a href="pracownik.php" class="btn btn-secondary mt-3">⬅ Powrót</a>

</div>

</body>
</html>