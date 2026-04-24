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

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
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

    <div>
        <?php  
        if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['dottore_scelto'])) {
            // CORREZIONE: Uso di Prepared Statements per evitare SQL Injection
            $stmt = $conn->prepare(
                "SELECT 
                    CONCAT(m.nome, '-', m.cognome, '-', m.specializzazione, '-', 
                           p.data_visita, '-', p.ora_visita) AS preno 
                FROM prenotazioni p 
                JOIN medici m ON m.id_medico = p.id_medico 
                WHERE p.stato = 'prenotata' 
                AND m.id_medico = ? 
                ORDER BY m.cognome, p.data_visita, p.ora_visita"
            );

            /**
             * Bind an integer parameter to the prepared statement and execute the query
             * 
             * Binds the 'dottore_scelto' POST parameter as an integer to the prepared statement,
             * then executes the query and retrieves the result set.
             * 
             * @param int $_POST['dottore_scelto'] The ID of the selected doctor (integer)
             * @return mysqli_result The result set from the executed query
             */
            // Bind del parametro (i = integer)
            $stmt->bind_param("i", $_POST['dottore_scelto']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<table border='1' style='margin-top: 20px; width: 100%;'>";
                echo "<tr><th>Prenotazioni Disponibili</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . htmlspecialchars($row["preno"]) . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='margin-top: 20px; color: #666;'>Nessuna data disponibile</p>";
            }

            $stmt->close();
        }
        ?>
    </div>

</body>
</html>

<?php
$conn->close();
?>