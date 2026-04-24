<?php
// Connessione al database
require_once 'connect_dbms.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selezione Dottore</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🦷 Dentista</h1>
        <p class="subtitle">Seleziona un dottore</p>

        <div class="info-box">
            Scegli un dottore dalla lista per procedere con la prenotazione.
        </div>

        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-group">
                <label for="dottore">Seleziona Dottore:</label>
                <select id="dottore" name="dottore_scelto" required>
                    <option value="">-- Scegli un dottore --</option>
                    <?php
                    $sql = "SELECT id_medico, nome, cognome, specializzazione FROM medici ORDER BY cognome";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $id = htmlspecialchars($row["id_medico"]);
                            $nome = htmlspecialchars($row["nome"]);
                            $cognome = htmlspecialchars($row["cognome"]);
                            $spec = htmlspecialchars($row["specializzazione"]);
                            echo "<option value=\"$id\">Dr. $nome $cognome - $spec</option>";
                        }
                    } else {
                        echo "<option value=\"\" disabled>Nessun dottore disponibile</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit">Seleziona</button>
        </form>

    </div>
    <div class="results-container">
        <?php  
            if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['dottore_scelto'])){
                $id_scelto = intval($_POST["dottore_scelto"]);
                $sql = "SELECT CONCAT( nome, '-', cognome, '-', specializzazione, '-', data_visita, '-', ora_visita ) AS preno FROM prenotazioni, medici WHERE stato = 'prenotata' AND medici.id_medico = prenotazioni.id_medico AND medici.id_medico=".$id_scelto." ORDER BY medici.cognome, prenotazioni.data_visita, prenotazioni.ora_visita;";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0) {
                    echo '<div class="results-title">📅 Prenotazioni Trovate</div>';
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="result-item">' . htmlspecialchars($row["preno"]) . '</div>';
                    }
                } else {
                    echo '<div class="no-results">Nessuna data disponibile</div>';
                }
            }
        ?>
    </div>



</body>
</html>

<?php
$conn->close();
?>

