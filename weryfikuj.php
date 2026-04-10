<!DOCTYPE html>

<html lang="pl">
<head>
<meta charset="utf-8" />
<title>Logowanie</title>
</head>
<body>

<?php
session_start();

if(!isset($_POST['user']) || !isset($_POST['pass'])){
    die("Brak danych logowania");
}

$user = $_POST['user'];
$pass = $_POST['pass'];

$link = mysqli_connect("127.0.0.1","dm81079_z14","Dawidek7003#","dm81079_z14");

if(!$link){
    die("Błąd połączenia: ".mysqli_connect_errno()." ".mysqli_connect_error());
}

mysqli_set_charset($link, "utf8");

// =====================
// 1. PRACOWNIK
// =====================
$sql = "SELECT * FROM pracownik WHERE login = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rekord = mysqli_fetch_assoc($result);

if($rekord && $rekord['haslo'] == $pass){
    $_SESSION['loggedin'] = true;
    $_SESSION['rola'] = 'pracownik';
    $_SESSION['idp'] = $rekord['idp'];
    $_SESSION['login'] = $rekord['login'];

    // przyszłościowo:
    header("Location: pracownik.php");
}
else {
    // =====================
    // 2. COACH
    // =====================
    $sql = "SELECT * FROM coach WHERE login = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rekord = mysqli_fetch_assoc($result);

    if($rekord && $rekord['haslo'] == $pass){
        $_SESSION['loggedin'] = true;
        $_SESSION['rola'] = 'coach';
        $_SESSION['idc'] = $rekord['idc'];
        $_SESSION['login'] = $rekord['login'];

        // przyszłościowo:
        header("Location: pracownik.php");
    }
    else {
        echo "Niepoprawny login lub hasło!";
    }
}

mysqli_close($link);
?>

</body>
</html>
