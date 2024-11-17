<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elections Amicale UIT'23</title>
    <style>
        body {
            background-color: #f5f5f5;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            background-color: #f0f8ff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 75%; 
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="submit"],
        input[type="radio"] {
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="submit"],
        input[type="radio"] {
            background-color: #3498db;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover,
        input[type="radio"]:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: #fff;
        }

        p {
            margin-top: 10px;
            color: #777;
        }

        h1 {
            text-align: center;
            color: #3498db;
        }

        .error-message {
            color: red;
            margin-top: 5px;
        }

        /* Popup style */
        .popup-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
        }

        .close-btn {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

       
        /* Style for grid layout */
        .candidate-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        align-items: stretch; 
        overflow-y: auto;
        max-height: 600px;
        margin: auto;
        width: 90%;
        max-width: 1200px;
         }

    .candidate {
        flex: 0 1 calc(30% - 10px); 
        margin-bottom: 20px;
        box-sizing: border-box;
        height: auto; 
        overflow: hidden;
        border: 2px solid #3498db;
        border-radius: 10px;
        background-color: #ffffff;
        display: flex;
        flex-direction: column; 
    }

    .candidate img {
        max-width: 100%;
        max-height: 100px;
        object-fit: cover;
    }

    .candidate-content {
        padding: 10px;
        flex-grow: 1; 
    }

    .candidate input {
        margin-top: auto; 
    }


    .form-image {
        max-width: 100%;
        height: auto;
        margin-bottom: 15px; 
    }

    /* Additional styles for the form container */
    form {
        text-align: center; 
        background-color: #f0f8ff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        width: 75%; 
        border: 2px solid #000; 
        margin: 20px; 
    }

    form input[type="text"],
    form input[type="submit"] {
        width: 50%; 
        margin: 0 auto; 
        display: block; 
    }


    </style>
</head>

<body>
    
    <div>
        <h1></h1>

        

        <?php
        // Require the database connection file
        require_once('dbconnexion.php');

        $codeInputVisible = true;
        $errorMessage = "";

        if (isset($_POST['connect'])) {
            $enteredCode = $_POST['code'];

            // Check if the voter has already voted
            $checkVoteQuery = "SELECT * FROM votes WHERE code_voter = '$enteredCode'";
            $checkVoteResult = $db->query($checkVoteQuery);

            if ($checkVoteResult) {
                if ($checkVoteResult->num_rows > 0) {
                    // Display a message ( oter has already voted, )
                    $errorMessage .= "<p class='error-message'>Vous avez déjà voté.</p>";
                    $codeInputVisible = true;
                } else {
                    // Voter has not voted yet, proceed with the code logic

                    // SQL query  with user input 
                    $query = "SELECT * FROM voters WHERE code = '$enteredCode'";
                    $result = $db->query($query);

                    if ($result) {
                        if ($result->num_rows > 0) {
                            // Code exists, user is connected

                            // Display candidates with radio buttons
                            echo "<form method='post'>";
                            echo "<p><strong>Sélectionnez votre candidat et cliquez sur Valider:</strong></p>";
                
                            // Use the candidate-container div for grid layout
                            echo "<div class='candidate-container'>";
                
                            // Fetch the "niveau" value of the logged-in voter
                            $fetchNiveauQuery = "SELECT niveau FROM voters WHERE code = '$enteredCode'";
                            $fetchNiveauResult = $db->query($fetchNiveauQuery);
                
                            if ($fetchNiveauResult && $fetchNiveauResult->num_rows > 0) {
                                $row = $fetchNiveauResult->fetch_assoc();
                                $voterNiveau = $row['niveau'];
                
                                // Define "niveau" values for each group using a switch statement
                                switch ($voterNiveau) {
                                    case 'L1SG':
                                    case 'L2SG':
                                    case 'L3SG-FIN':
                                    case 'L3SG-MNG':
                                        $niveauValues = array('L1SG', 'L2SG', 'L3SG-FIN', 'L3SG-MNG');
                                        break;
                
                                    case 'M1MRH':
                                    case 'M1MAE':
                                    case 'M1CCA': 
                                    case 'M2MRH':
                                    case 'M2MAE':
                                    case 'M2CCA':
                                    case 'M2INF':
                                        $niveauValues = array('M1MRH', 'M1MAE', 'M1CCA', 'M2MRH', 'M2MAE', 'M2CCA', 'M2INF');
                                        break;
                
                                    case 'L1DP':
                                    case 'L2DP':
                                    case 'L3DP':
                                        $niveauValues = array('L1DP', 'L2DP', 'L3DP');
                                        break;
                
                                    case 'M1DEA':
                                    case 'M1DTN':
                                    case 'M2DEA':
                                    case 'M2DTN':
                                        $niveauValues = array('M1DEA', 'M1DTN', 'M2DEA', 'M2DTN');
                                        break;
                
                                    case 'L1TI':
                                    case 'L2TI':
                                    case 'L3SC':
                                    case 'L3IR':
                                    case 'IGL1':
                                        $niveauValues = array('L1TI', 'L2TI', 'L3SC', 'L3IR', 'IGL1');
                                        break;
                
                                    case 'M1ISI':
                                    case 'M1SSI':
                                    case 'M2ISI':
                                    case 'M2SSI':
                                    case 'IGL2':
                                    case 'IGL3':
                                        $niveauValues = array('M1ISI', 'M1SSI', 'M2ISI', 'M2SSI', 'IGL2', 'IGL3');
                                        break;
                
                                    case 'BBA1':
                                    case 'BBA2':
                                    case 'BBA3':
                                        $niveauValues = array('BBA1', 'BBA2', 'BBA3');
                                        break;
                
                                    default:
                                        // Handle the case where the "niveau" doesn't match any group
                                        $errorMessage .= "<p class='error-message'>Invalid niveau value.</p>";
                                        $codeInputVisible = true;
                                        break;
                                }
                
                                $fetchNiveauResult->close();
                            } else {
                                // Handle the case where the "niveau" couldn't be fetched
                                $errorMessage .= "<p class='error-message'>Failed to fetch 'niveau' for the entered code.</p>";
                                $codeInputVisible = true;
                            }
                
                            // Modify the candidates query to filter candidates by "niveau" 
                            $niveauValuesString = implode("','", $niveauValues);
                            $candidatesQuery = "SELECT * FROM candidates WHERE niveau IN ('$niveauValuesString')";
                            $candidatesResult = $db->query($candidatesQuery);
                
                            if ($candidatesResult) {
                                $candidateCounter = 1; // Initialize a counter for assigning unique values to candidates
                
                                while ($row = $candidatesResult->fetch_assoc()) {
                                    // Check if the candidate's "niveau" is in the array of allowed niveau values
                                    if (in_array($row['niveau'], $niveauValues)) {
                                        echo "<div class='candidate'>";
                                        echo "<img src='data:image/jpeg;base64," . $row['photo'] . "' alt='Candidate Photo'>";
                                        echo "<div class='candidate-content'>";
                                        echo "<p><strong>" . $row['prenom'] . " " . $row['nom'] . "</strong></p>";
                                        echo "<p>Niveau: " . $row['niveau'] . "</p>";
                                        echo "</div>";
                                        echo "<label for='candidate" . $candidateCounter . "'><input type='radio' name='selected_candidate' id='candidate" . $candidateCounter . "' value='" . $row['nom'] . " " . $row['prenom'] . "' required></label>";

                                        echo "</div>";
                                        $candidateCounter++;
                                    }
                                }
                
                                $candidatesResult->close();
                            } else {
                                $errorMessage .= "<p class='error-message'>Error fetching candidates.</p>";
                            }
                
                            echo "</div>"; 
                
                            // Add a hidden input field to store the 'code' value
                            echo "<input type='hidden' name='code' value='$enteredCode'>";
                
                            echo "<input type='submit' name='submit_vote' value='Valider'>";
                            echo "</form>";
                            $codeInputVisible = false;
                        } else {
                            // Display "code incorrect" message (Code doesn't exist)
                            $errorMessage .= "<p class='error-message'>Code incorrect. Veuillez saisir un code valide.</p>";
                            $codeInputVisible = true;
                        }
                    
                
                    } else {
                        // Handle the error (Query execution failed)
                        $errorMessage .= "<p class='error-message'>Query execution error: " . $db->error . "</p>";
                        $codeInputVisible = true;
                    }
                }
            } else {
                // display an error message (Error checking vote status)
                $errorMessage .= "<p class='error-message'>Error checking vote status.</p>";
                $codeInputVisible = true;
            }
        }

        if ($codeInputVisible) {
            // Display the initial form with the code input field
            echo "<form method='post'>";
            
            echo "<img src='https://univ-internationale.com/sites/default/files/logo_fr_0.png'  alt='logo' class='form-image'>";
            // echo "<img src='https://i.ibb.co/gZ0H9wC/elec.png' style='max-width: 100%; margin-bottom: 50px;' alt='logo' class='form-image'>";                      
            echo "<label for='code'>Saisissez votre code électeur:</label>";
            echo "<input type='text' name='code' id='code' required>";
            echo "<br>";
            echo "<input type='submit' name='connect' value='Connexion'>";
            echo $errorMessage;
            echo "</form>";
        }

        if (isset($_POST['submit_vote'])) {
            $selectedCandidate = $db->real_escape_string($_POST['selected_candidate']);
            $enteredCode = $_POST['code']; // Retrieve 'code' from the hidden input field

            // Fetch the 'niveau' from the 'voters' table based on the entered code
            $fetchNiveauQuery = "SELECT niveau FROM voters WHERE code = '$enteredCode'";
            $fetchNiveauResult = $db->query($fetchNiveauQuery);
        
            if ($fetchNiveauResult && $fetchNiveauResult->num_rows > 0) {
                $row = $fetchNiveauResult->fetch_assoc();
                $niveau = $row['niveau'];
        
                // Insert the vote into the 'votes' table with 'code_voter', 'choice', and 'niveau'
                $insertVoteQuery = "INSERT INTO votes (code_voter, choice, niveau) VALUES ('$enteredCode', '$selectedCandidate', '$niveau')";
                
                if ($db->query($insertVoteQuery)) {
                    // Display the pop-up message (JavaScript)
                    echo "<div class='popup-container' id='popupContainer'>
                            <div class='popup'>
                                <p>Votre vote a été validé avec succès.</p>
                                <button class='close-btn' onclick='closePopup()'>Close</button>
                            </div>
                        </div>
                        <script>
                            function closePopup() {
                                document.getElementById('popupContainer').style.display = 'none';
                            }
                            document.getElementById('popupContainer').style.display = 'flex';
                        </script>";
                } else {
                    $errorMessage .= "<p class='error-message'>Failed to submit your vote. Please try again later.</p>";
                    $codeInputVisible = true;
                }
            } else {
                // Handle the case where the 'niveau' couldn't be fetched
                $errorMessage .= "<p class='error-message'>Failed to fetch 'niveau' for the entered code.</p>";
                $codeInputVisible = true;
            }
        
            // Close the result set for the fetchNiveau query
            $fetchNiveauResult->close();
        }
        ?>
    </div>
</body>

</html>