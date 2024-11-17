<?php
// Include the database connection file 
require_once 'dbconnexion.php';

// Function to fetch candidate data by CIN/Passport
function getCandidateByCIN($db, $cin_pass) {
    $select_query = "SELECT * FROM candidates WHERE cin_pass = '$cin_pass'";
    $result = mysqli_query($db, $select_query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        return null;
    }
}

// Function to delete a candidate by CIN/Passport
function deleteCandidate($db, $cin_pass) {
    $delete_query = "DELETE FROM candidates WHERE cin_pass = '$cin_pass'";
    return mysqli_query($db, $delete_query);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_candidate'])) {
        // Handle candidate addition
        $cin_pass = $_POST['cin_pass'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $niveau = $_POST['niveau'];

        // Check if a new image has been uploaded
        if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
            // Handle image upload by encoding it as base64
            $photo = base64_encode(file_get_contents($_FILES['photo']['tmp_name']));
        } else {
            $photo = '';  
        }

        // Insert the candidate into the "candidates" table
        $insert_query = "INSERT INTO candidates (cin_pass, nom, prenom, niveau, photo) 
                         VALUES ('$cin_pass', '$nom', '$prenom', '$niveau', '$photo')";

        if (mysqli_query($db, $insert_query)) {
            // candidate added to the database succesfullu
        } else {
            // Handle the database query error 
        }
    } elseif (isset($_POST['modify_candidate'])) {
        // Handle candidate modificatio
        $cin_pass = $_POST['cin_pass'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $niveau = $_POST['niveau'];

        // Check if a new image has been uploaded
        if (isset($_FILES['mod_photo']) && is_uploaded_file($_FILES['mod_photo']['tmp_name'])) {
            // Handle image upload by encoding it as base64
            $photo = base64_encode(file_get_contents($_FILES['mod_photo']['tmp_name']));
        } else {
            // keep the existing photo (No new image uploaded)
            $candidateData = getCandidateByCIN($db, $cin_pass);
            if ($candidateData) {
                $photo = $candidateData['photo'];
            } else {
                $photo = '';  
            }
        }

        // Update the candidate in the "candidates" table
        $update_query = "UPDATE candidates 
                         SET nom = '$nom', prenom = '$prenom', niveau = '$niveau', photo = '$photo' 
                         WHERE cin_pass = '$cin_pass'";

        if (mysqli_query($db, $update_query)) {
            // candidate updated in the database succesfully
        } else {
            // Handle the database query error 
        }
    }

}

if (isset($_GET['delete_candidate'])) {
    // Delete the candidate if the 'delete_candidate' parameter is set
    $cin_pass = $_GET['delete_candidate'];
    if (deleteCandidate($db, $cin_pass)) {
        // candidate deleted from the database successfully 
    } else {
        // Handle the database query error
    }
}

// Retrieve and display the list of candidates
$select_query = "SELECT * FROM candidates";
$result = mysqli_query($db, $select_query);
$candidates = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $candidates[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Gestion des candidats</title>
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
            z-index: 2; 
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
            z-index: 1; 
        }

        .framed-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 2px solid black; 
            border-radius: 50px; 
        }

        .framed-table th {
            border: 2px solid black;
            padding: 8px;
            text-align: left;
            background-color: #45b5ff;
        }

        .framed-table td {
            border: 2px solid black;
            padding: 8px;
            text-align: left;
        }


        .add-candidate-popup,
        .modify-candidate-popup {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
            overflow: auto;
        }

        .popup-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            width: 60%;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .form-input {
            display: block;
            margin: 10px 0;
        }
    </style>
</head>

<body>
   
    <div class="navbar">
        <a href="manage_candidates.php">Gestion des candidats</a>
        <a href="manage_voters.php">Gestion des élections</a>
    </div>

    <div class="content">
    <h1>Gestion des candidats</h1>

        <!-- Display the list of candidates within a framed table -->
        <div style="overflow-x:auto;">
            <table class="framed-table">
                <tr>
                    <th>CIN/Passport</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Niveau</th>
                    <th>Photo</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($candidates as $candidate) { ?>
                    <tr>
                        <td><?php echo $candidate['cin_pass']; ?></td>
                        <td><?php echo $candidate['nom']; ?></td>
                        <td><?php echo $candidate['prenom']; ?></td>
                        <td><?php echo $candidate['niveau']; ?></td>
                        <td><img src="data:image/jpeg;base64,<?php echo $candidate['photo']; ?>" alt="Candidate Photo" width="100"></td>
                        <td>
                            <!-- Add the "Modify" button for each candidate -->
                            <button onclick="modifyCandidate('<?php echo $candidate['cin_pass']; ?>')">Modifier</button>

                            <!-- Add the "Delete" button for each candidate -->
                            <button onclick="confirmDelete('<?php echo $candidate['cin_pass']; ?>')">Supprimer</button>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>


        <!-- Button to open the add candidate pop-up -->
        <br>
        <button onclick="openAddCandidatePopup()">Ajouter un candidat</button>

                <!-- Add Candidate Pop-up -->
        <div class="add-candidate-popup" id="addCandidatePopup">
            <div class="popup-content">
                <span class="close-button" onclick="closeAddCandidatePopup()">&times;</span>
                <h2>Add New Candidate</h2>
                <form method="post" enctype="multipart/form-data">
                    <label class="form-input" for="cin_pass">CIN/Passport:</label>
                    <input type="text" name="cin_pass" required>
                    
                    <label class="form-input" for="nom">Nom:</label>
                    <input type="text" name="nom" required>
                    
                    <label class="form-input" for="prenom">Prénom:</label>
                    <input type="text" name="prenom" required>

                    <label class="form-input" for="niveau">Niveau:</label>
                    <select name="niveau" id="add_niveau" required>
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
                            <option value="M2INF">M2INF</option>
                            <option value="M2MRH">M2MRH</option>
                            <option value="M2MAE">M2MAE</option>
                            <option value="M2CCA">M2CCA</option>
                           
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
                    
                    <label class="form-input" for="photo">Photo:</label>
                    <input type="file" name="photo" required>
                    
                    <button type="submit" name="add_candidate">Ajouter candidat</button>
                </form>
            </div>
        </div>

        <!-- Modify Candidate Pop-up -->
        <div class="modify-candidate-popup" id="modifyCandidatePopup">
            <div class="popup-content">
                <span class="close-button" onclick="closeModifyCandidatePopup()">&times;</span>
                <h2>Modifier candidat</h2>
                <form method="post" enctype="multipart/form-data">
                    <!-- Hidden input to store the CIN/Passport for modification -->
                    <input type="hidden" name="cin_pass" id="mod_cin_pass">
                    
                    <label class="form-input" for="nom">Nom:</label>
                    <input type="text" name="nom" id="mod_nom" required>
                    
                    <label class="form-input" for="prenom">Prénom:</label>
                    <input type="text" name="prenom" id="mod_prenom" required>

                    <label class="form-input" for="niveau">Niveau:</label>
                    <select name="niveau" id="mod_niveau" required>
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
                            <option value="M2INF">M2INF</option>
                            <option value="M2MRH">M2MRH</option>
                            <option value="M2MAE">M2MAE</option>
                            <option value="M2CCA">M2CCA</option>
                          
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
                    
                    <!-- Display existing photo (if available) -->
                    <label class="form-input" for="mod_photo">Photo:</label>
                    <img id="mod_photo_preview" alt="Existing Photo" width="100">
                    <input type="file" name="mod_photo" accept="image/*">
                    
                    <button type="submit" name="modify_candidate">Modifier candidat</button>
                </form>
            </div>
        </div>


    <script>
        function openAddCandidatePopup() {
        document.getElementById("addCandidatePopup").style.display = "block";
        }

        function closeAddCandidatePopup() {
            document.getElementById("addCandidatePopup").style.display = "none";
        }

        function modifyCandidate(cin_pass) {
        // Fetch candidate data and fill the modify candidate form fields
        const candidateData = <?php echo json_encode($candidates); ?>;
        const candidate = candidateData.find((c) => c.cin_pass === cin_pass);

        if (candidate) {
            document.getElementById("mod_cin_pass").value = candidate.cin_pass;
            document.getElementById("mod_nom").value = candidate.nom;
            document.getElementById("mod_prenom").value = candidate.prenom;
            document.getElementById("mod_niveau").value = candidate.niveau;

            // Check if the candidate has an existing photo
            if (candidate.photo) {
                // Set the existing photo for preview
                const imgElement = document.getElementById("mod_photo_preview");
                imgElement.src = "data:image/jpeg;base64," + candidate.photo;
            }

            document.getElementById("modifyCandidatePopup").style.display = "block";
        }
        }

        function closeModifyCandidatePopup() {
            document.getElementById("modifyCandidatePopup").style.display = "none";
        }

        function confirmDelete(cin_pass) {
            if (confirm("Êtes-vous surs de vouloir supprimer ce candidat?")) {
                // If the user confirms, redirect to the delete_candidate endpoint
                window.location.href = "manage_candidates.php?delete_candidate=" + cin_pass;
            }
        }
    </script>
</body>
</html>
