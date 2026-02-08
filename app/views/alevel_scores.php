<?php // View: A-Level scores entry and grade points
// If no subjects, prompt to add them first
if (empty($viewData['subjects'] ?? [])) { // Check subjects presence
    echo '<div class="card"><p>Please add A-Level subjects first.</p><a class="nav-link" href="/Charm/app/controllers/DashboardController.php?action=alevel_subjects">Add A-Level Subjects</a></div>'; // Prompt
    return; // Stop rendering
}

// Render form for grades per subject with category hidden inputs
echo '<div class="card">';
    echo '<h3>Advanced Level Scores</h3>'; 
echo '<p>Principal subjects use A=6, B=5, C=4, D=3, E=2, O=1, F=0. Subsidiaries D/C=1, P/F=0.</p>'; 
echo '<form method="post" action="/Charm/app/controllers/DashboardController.php?action=alevel_scores">';
foreach ($viewData['subjects'] as $s) { // Loop over subjects
    $name = $s['subject_name']; // Name
    $cat = $s['category']; // Category
    echo '<label>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ' (' . $cat . ')</label>'; // Label
    echo '<input type="hidden" name="category[' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ']" value="' . $cat . '">'; // Hidden category
    if ($cat === 'principle') { // Principle options
        echo '<select name="grade[' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ']">';
        foreach ($alevelPrinciplePoints as $g => $p) { echo '<option value="' . $g . '">' . $g . '</option>'; }
        echo '</select>'; // End select
    } else { // Subsidiary options
        echo '<select name="grade[' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ']">';
        foreach ($alevelSubsidiaryPoints as $g => $p) { echo '<option value="' . $g . '">' . $g . '</option>'; }
        echo '</select>'; // End select
    }
}
echo '<button type="submit" class="btn btn-primary">Save Advanced Level Scores</button>'; // Submit
echo '</form>'; 
echo '</div>'; 

// Next step guidance
echo '<div class="card"><p>Next: compute weights.</p><a class="nav-link" href="/Charm/app/controllers/DashboardController.php?action=weight">Compute Weights</a></div>'; 
