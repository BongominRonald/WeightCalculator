<?php // View: O-Level scores entry and calculation
// If no subjects are present, prompt user to add them
if (empty($viewData['subjects'] ?? [])) { // Check subjects existence
    echo '<div class="card"><p>Please add O-Level subjects first.</p><a class="nav-link" href="/Charm/app/controllers/DashboardController.php?action=olevel_subjects">Add Subjects</a></div>'; // Prompt
    return; // Stop rendering
}

// Render form for selecting grades per subject
echo '<div class="card">';
echo '<h3>Ordinary Level Scores</h3>'; 
echo '<p>Select grades for each subject. Distinction 0.3, Credit 0.2, Pass 0.1, Fail 0.</p>'; 
echo '<form method="post" action="/Charm/app/controllers/DashboardController.php?action=olevel_scores">';
foreach ($viewData['subjects'] as $s) { // Loop subjects
    $name = $s['name']; // Subject name
    echo '<label>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</label>'; // Label
    echo '<select name="grade[' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ']">'; // Grade select
    foreach ($olevelGradeMap as $grade => $meta) { // Loop grade options
        echo '<option value="' . $grade . '">' . $grade . '</option>'; // Option
    }
    echo '</select>'; // End select
}
echo '<button type="submit" class="btn btn-primary">Save Ordinary Level Scores</button>'; // Submit button
echo '</form>'; 
echo '</div>'; 

// Next step guidance
echo '<div class="card"><p>Next: set Advanced Level subjects.</p><a class="nav-link" href="/Charm/app/controllers/DashboardController.php?action=alevel_subjects">Set Advanced Level Subjects</a></div>'; 
