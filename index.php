<?php
declare(strict_types=1);
session_start();


// POŁĄCZENIE Z BAZĄ
$host = "127.0.0.1";
$user = "dm81079_z14";
$password = "Dawidek7003#";
$dbname = "dm81079_z14";

$conn = mysqli_connect($host, $user, $password, $dbname);

// Sprawdzenie połączenia
if (!$conn) {
    die("Błąd połączenia z bazą danych: " . mysqli_connect_error());
}

// Ustawienie znaków UTF-8
mysqli_set_charset($conn, "utf8");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2746830856581274"
     crossorigin="anonymous"></script>
    
<meta charset="utf-8">
<title>Panel E-learning</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

     
<style>
body {
    background-color: #f5f7fa;
}

.sidebar {
    height: 100vh;
    background: #1e293b;
    color: white;
    padding: 20px;
}

.sidebar a {
    color: #cbd5f5;
    display: block;
    margin: 10px 0;
    text-decoration: none;
}

.sidebar a:hover {
    color: white;
}

.card {
    border-radius: 15px;
}

.lekcja-box {
    background-color: #ffffff;
    border: 2px solid #1e293b;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    max-width: 1000px;
    margin-top: 15px;
    word-wrap: break-word;      /* łamanie długich słów */
}

.lekcja-box img {
    max-width: 50%;   /* obraz nie wyjdzie poza szerokość boxa */
    max-height: 500px; /* maksymalna wysokość */
    width: auto;
    height: auto;
    display: block;
    margin-top: 10px;
    object-fit: contain; /* dopasowanie obrazu wewnątrz boxa */
}

.lekcja-box video {
    max-width: 50%;   /* obraz nie wyjdzie poza szerokość boxa */
    max-height: 500px; /* maksymalna wysokość */
    width: auto;
    height: auto;
    display: block;
    margin-top: 10px;
    object-fit: contain; /* dopasowanie obrazu wewnątrz boxa */
}

.lekcja-box audio {
    max-width: 50%;   /* obraz nie wyjdzie poza szerokość boxa */
    max-height: 500px; /* maksymalna wysokość */
    width: auto;
    height: auto;
    display: block;
    margin-top: 10px;
    object-fit: contain; /* dopasowanie obrazu wewnątrz boxa */
}

</style>
</head>

<body class="d-flex flex-column min-vh-100">


<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
<div class="col-md-2 sidebar">
    <a href="index.php"><h4>📚 E-learning</h4></a>
    <hr>
    <?php
    // Pobranie wszystkich lekcji
    $query_sidebar = "SELECT idl, nazwa FROM lekcje ORDER BY idl ASC";
    $result_sidebar = mysqli_query($conn, $query_sidebar);

    $licznik = 1;
    while ($row_sidebar = mysqli_fetch_assoc($result_sidebar)) {
        echo '<a href="?lekcja='.$row_sidebar['idl'].'">📘 Lekcja '.$licznik.': '.$row_sidebar['nazwa'].'</a>';
        $licznik++;
    }

    // Dodatkowy link do podsumowania
    echo '<a href="?lekcja=podsumowanie">📊 Podsumowanie</a>';
    ?>
    <hr>
    <a href="logowanie.php">🔐 Zaloguj się</a>
    <a href="rejestracja.php">👤 Zarejestruj się</a>
</div>

        <!-- GŁÓWNA TREŚĆ -->
        <div class="col-md-10 p-4">

            <h2>Witaj 👋</h2>
            <p>W celu uzyskania dostępu do pełni funkcjonalności <a href="logowanie.php">zaloguj się</a></p>

            <!-- STATYSTYKI -->
            <div class="row mb-4">
<?php
$lekcja = $_GET['lekcja'] ?? null;

echo "<div class='row mb-4'>";

// Nagłówek dynamiczny
echo "<h4>";
if ($lekcja !== null) {
    if ($lekcja === 'podsumowanie') {
        echo "Podsumowanie";
    } else {
        $idl = (int)$lekcja;
        echo "Lekcja $idl";
    }
} else {
    echo "Lekcja";
}
echo "</h4>";

// Box z treścią lekcji
echo "<div class='lekcja-box'>";

if ($lekcja !== null) {
    if ($lekcja === 'podsumowanie') {
        // Statyczne podsumowanie
        echo "<ul>
                <li><strong>Dysk twardy (HDD):</strong> trwałe przechowywanie danych; zapis magnetyczny na talerzach; dane pozostają po wyłączeniu komputera.</li>
                <li><strong>Pamięć RAM:</strong> tymczasowe przechowywanie danych dla procesora; szybki dostęp; dane znikają po wyłączeniu; większa pojemność = więcej programów w działaniu.</li>
                <li><strong>Karta graficzna (GPU):</strong> generuje i wyświetla obraz; odciąża procesor; umożliwia grafikę 3D i animacje; główni producenci: Nvidia, AMD, Intel.</li>
              </ul>";
    } else {
        // Wyświetlanie konkretnej lekcji z bazy
        $idl = (int)$lekcja;
        $query = "SELECT l.*, c.login AS autor 
                  FROM lekcje l 
                  LEFT JOIN coach c ON l.idc = c.idc 
                  WHERE l.idl = $idl";
        $result = mysqli_query($conn, $query);

        if ($row = mysqli_fetch_assoc($result)) {
            echo "<h5>".$row['nazwa']."</h5>";
            echo "<p style='font-size:0.9em; color:#555;'>Autor: ".$row['autor']."</p>";
            echo "<p>".$row['tresc']."</p>";

            if (!empty($row['plik_multimedialny'])) {
                $ext = strtolower(pathinfo($row['plik_multimedialny'], PATHINFO_EXTENSION));

                if (in_array($ext, ['mp4','webm','ogg'])) {
                    echo "<video controls autoplay muted loop>
                            <source src='".$row['plik_multimedialny']."'>
                          </video>";
                } elseif (in_array($ext, ['jpg','jpeg','png','gif'])) {
                    echo "<img src='".$row['plik_multimedialny']."' alt='Obraz lekcji'>";
                } elseif ($ext === 'mp3') {
                    echo "<audio controls autoplay>
                            <source src='".$row['plik_multimedialny']."'>
                          </audio>";
                } else {
                    echo "<p>Plik multimedialny nieobsługiwany</p>";
                }
            }
        } else {
            echo "<p>Nie znaleziono lekcji o ID $idl</p>";
        }
    }
} else {
    echo "<p>Wybierz lekcję z menu po lewej stronie 📚</p>";
}

echo "</div>"; // koniec lekcja-box
echo "</div>"; // koniec row
?>
            </div>



            </div>

        </div>
    </div>
</div>


</body>
</html>
