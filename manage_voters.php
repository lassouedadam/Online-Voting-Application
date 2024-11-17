<?php
// Include the database connection file
require_once 'dbconnexion.php';

// Generate random unique codes
function generateUniqueRandomCode($length = 6, $db) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $codeExists = true;

    do {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        $check_query = "SELECT COUNT(*) as count FROM voters WHERE code = '$code'";
        $result = mysqli_query($db, $check_query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $codeExists = $row['count'] > 0;
        } else {
            // Handle the database query error 
            $codeExists = true;
        }

    } while ($codeExists);

    return $code;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_codes'])) {
    // Generate and add random codes to the "voters" table
    $number_of_entries = intval($_POST['number_of_entries']);
    $selected_niveau = mysqli_real_escape_string($db, $_POST['niveau']);

    for ($i = 0; $i < $number_of_entries; $i++) {
        $random_code = generateUniqueRandomCode(6, $db);
        $insert_query = "INSERT INTO voters (code, niveau) VALUES ('$random_code', '$selected_niveau')";
        $generated_codes = isset($generated_codes) ? $generated_codes : [];
        for ($i = 0; $i < $number_of_entries; $i++) {
            $random_code = generateUniqueRandomCode(6, $db);
            $insert_query = "INSERT INTO voters (code, niveau) VALUES ('$random_code', '$selected_niveau')";
    
            if (mysqli_query($db, $insert_query)) {
                // code added to the database successfully 
                $generated_codes[] = $random_code;
            } else {
                // Handle the database query error 
            }
        }
    }
    

    header("Location: manage_voters.php"); // Redirect to the same page to show updated data
}

// Retrieve the number of participants
$select_participants_query = "SELECT COUNT(*) as participant_count FROM voters";
$participants_result = mysqli_query($db, $select_participants_query);
$participant_count = ($participants_result) ? mysqli_fetch_assoc($participants_result)['participant_count'] : 0;

// Retrieve the percentage of participation
$select_votes_query = "SELECT COUNT(*) as vote_count FROM votes";
$votes_result = mysqli_query($db, $select_votes_query);
$vote_count = ($votes_result) ? mysqli_fetch_assoc($votes_result)['vote_count'] : 0;

$percentage_participation = ($participant_count > 0) ? ($vote_count / $participant_count) * 100 : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des élections</title>
    <style>
        body {
            background-color: white;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #333;
            height: 100vh;
            width: 200px;
            position: fixed;
            top: 0;
            left: 0;
            overflow: auto;
        }
        .navbar a {
            padding: 15px;
            color: white;
            display: block;
            text-decoration: none;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }
        .info-box {
            background-color: #45b5ff;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="manage_candidates.php">Gestion des candidats</a>
        <a href="manage_voters.php">Gestion des élections</a>
    </div>
    <div class="content">
        <h1>Génerer des codes électeurs</h1>

        <form method="post">
            <label for="number_of_entries">Nombre de codes à générer:</label>
            <input type="number" name="number_of_entries" min="1" required>

            <!-- Add the "niveau" select field with optgroups and options -->
            <label for="niveau">Niveau:</label>
            <select name="niveau" required>
                <optgroup label="Licence Gestion">
                    <option value="L1SG">L1SG</option>
                    <option value="L2SG">L2SG</option>
                    <option value="L3SG-FIN">L3SG-FIN</option>
                    <option value="L3SG-MNG">L3SG-MNG</option>
                </optgroup>
                <optgroup label="Master Gestion">
                    <option value="M1MRH">M1MRH</option>
                    <option value="M1MAE">M1MAE</option>
                    <option value="M1CCA">M1CCA</option>
                    <option value="M2MRH">M2MRH</option>
                    <option value="M2MAE">M2MAE</option>
                    <option value="M2CCA">M2CCA</option>
                    <option value="M2INF">M2INF</option>
                </optgroup>
                <optgroup label="Licence Droit">
                    <option value="L1DP">L1DP</option>
                    <option value="L2DP">L2DP</option>
                    <option value="L3DP">L3DP</option>
                </optgroup>
                <optgroup label="Master Droit">
                    <option value="M1DEA">M1DEA</option>
                    <option value="M1DTN">M1DTN</option>
                    <option value="M2DEA">M2DEA</option>
                    <option value="M2DTN">M2DTN</option>
                </optgroup>
                <optgroup label="Licence Informatique">
                    <option value="L1TI">L1TI</option>
                    <option value="L2TI">L2TI</option>
                    <option value="L3SC">L3SC</option>
                    <option value="L3IR">L3IR</option>
                    <option value="IGL1">IGL1</option>
                </optgroup>
                <optgroup label="Master Informatique">
                    <option value="M1ISI">M1ISI</option>
                    <option value="M1SSI">M1SSI</option>
                    <option value="M2ISI">M2ISI</option>
                    <option value="M2SSI">M2SSI</option>
                    <option value="IGL2">IGL2</option>
                    <option value="IGL3">IGL3</option>
                </optgroup>
                <optgroup label="BBA">
                    <option value="BBA1">BBA1</option>
                    <option value="BBA2">BBA2</option>
                    <option value="BBA3">BBA3</option>
                </optgroup>
            </select>

            <button type="submit" name="generate_codes">Générer</button>
        </form>
        
        <h1>Codes électeurs</h1>

        <?php
        // Retrieve and display codes for each "niveau"
        $select_codes_query = "SELECT niveau, GROUP_CONCAT(code) AS codes FROM voters GROUP BY niveau";
        $codes_result = mysqli_query($db, $select_codes_query);

        if ($codes_result) {
            while ($row = mysqli_fetch_assoc($codes_result)) {
                $niveau = $row['niveau'];
                $codes = $row['codes'];

                // Split codes into an array and join with line breaks
                $formatted_codes = implode("<br>", explode(",", $codes));

                // Determine the label based on the "niveau"
                $label = getNiveauLabel($niveau);

                echo "<div class='info-box'>";
                echo "<strong>Codes pour le niveau $niveau ($label):</strong> <br>$formatted_codes";
                echo "</div>";
            }
        } else {
            echo "<p>Error fetching codes.</p>";
        }

        // Function to get the label based on the niveau
        function getNiveauLabel($niveau)
        {
            // Define an array with the "optgroup" and "option" structure
            $niveauLabels = [
                'Licence Gestion' => ['L1SG', 'L2SG', 'L3SG-FIN', 'L3SG-MNG'],
                'Master Gestion'  => ['M1MRH', 'M1MAE', 'M1CCA', 'M2INF', 'M2MRH', 'M2MAE', 'M2CCA'],
                'Licence Droit'   => ['L1DP', 'L2DP', 'L3DP'],
                'Master Droit'    => ['M1DEA', 'M1DTN', 'M2DEA', 'M2DTN'],
                'Licence Informatique' => ['L1TI', 'L2TI', 'L3SC', 'L3IR', 'IGL1'],
                'Master Informatique'  => ['M1ISI', 'M1SSI', 'M2ISI', 'M2SSI', 'IGL2', 'IGL3'],
                'BBA' => ['BBA1', 'BBA2', 'BBA3'],
             
            ];

            foreach ($niveauLabels as $label => $niveaux) {
                if (in_array($niveau, $niveaux)) {
                    return $label;
                }
            }

            return 'Autre';
        }
        ?>




    
        <br>

        <h1>Participation</h1>

        <div class="info-box">
            <strong>Nombre de participants:</strong> <?php echo $participant_count; ?>
        </div>

        <br>

        <div class="info-box">
            <strong>Pourcentage de participation:</strong> <?php echo number_format($percentage_participation, 2) . '%'; ?>
        </div>

        <!-- New box for displaying election results -->
        <div class="result-box">
            <h1>Résultats</h1>

            <?php
            // Define the groups of "niveau" values
            $niveauGroups = [
                'Licence Gestion' => ['L1SG', 'L2SG', 'L3SG-FIN', 'L3SG-MNG'],
                'Master Gestion' => ['M1MRH', 'M1MAE', 'M1CCA', 'M2INF', 'M2MRH', 'M2MAE', 'M2CCA', 'M2INF'],
                'Licence Droit' => ['L1DP', 'L2DP', 'L3DP'],
                'Master Droit' => ['M1DEA', 'M1DTN', 'M2DEA', 'M2DT'],
                'Licence Informatique' => ['L1TI', 'L2TI', 'L3SC', 'L3IR', 'IGL1'],
                'Master Informatique' => ['M1ISI', 'M1SSI', 'M2ISI', 'M2SSI', 'IGL2', 'IGL'],
                'BBA' => ['BBA1', 'BBA2', 'BBA3'],
            ];

            // Retrieve and display results for each group of "niveau" values
            foreach ($niveauGroups as $groupLabel => $niveauValues) {
                echo "<p><strong>Résultats pour le groupe $groupLabel:</strong><br>";

                // Retrieve results for the current group
                $select_results_query = "SELECT choice, COUNT(*) as choice_count FROM votes
                                        WHERE niveau IN ('" . implode("','", $niveauValues) . "')
                                        GROUP BY choice";
                $results_result = mysqli_query($db, $select_results_query);

                if ($results_result) {
                    while ($row = mysqli_fetch_assoc($results_result)) {
                        $choice = $row['choice'];
                        $choice_count = $row['choice_count'];

                        echo "<strong> $choice:</strong> $choice_count votes<br>";
                    }
                } else {
                    echo "<p>Error fetching election results for $groupLabel.</p>";
                }

                echo "</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>