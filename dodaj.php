<?php
session_start();

$user = $_POST['user'];
$pass = $_POST['pass'];
$pass2 = $_POST['pass2'];

$link = mysqli_connect("127.0.0.1","dm81079_z14","Dawidek7003#","dm81079_z14");

if(!$link){
    die("Błąd: ".mysqli_connect_errno()." ".mysqli_connect_error());
}
mysqli_set_charset($link, "utf8");
// =====================
// WALIDACJA
// =====================
if ($user === '' || $pass === '' || $pass2 === '') {
    echo "Wypełnij wszystkie pola.";
    mysqli_close($link);
    exit();
}
if ($pass !== $pass2){
    echo "Hasła nie są identyczne.";
    mysqli_close($link);
    exit();
}
// =====================
// SPRAWDZENIE LOGINU
// =====================
$sql = "SELECT idp FROM pracownik WHERE login = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) > 0){
    echo "Użytkownik już istnieje.";
    mysqli_close($link);
    exit();
}
// =====================
// DODAWANIE PRACOWNIKA
// =====================
$sql = "INSERT INTO pracownik (login, haslo) VALUES (?, ?)";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "ss", $user, $pass);
mysqli_stmt_execute($stmt);
// przekierowanie
header("Location: logowanie.php");
exit();
mysqli_close($link);
?>