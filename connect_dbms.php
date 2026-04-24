<?php  
//credinziali
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "studio_dentistico"; 

//connessione 
$conn = new mysqli($servername, $username, $password, $dbname);

//verifica la connessione
if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}

//imposta il charset
$conn->set_charset("utf8");
?>

?>