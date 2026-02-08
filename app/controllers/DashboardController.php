<?php // Central controller for dashboard and academic flows
// Resolve project root directory
$root = dirname(__DIR__, 2); // Move up to project root
// Include database connection
require $root . '/config/db_connect.php'; // Shared PDO connection
// Include models
require $root . '/app/models/User.php'; // User model
require $root . '/app/models/OLevel.php'; // O-Level model
require $root . '/app/models/ALevel.php'; // A-Level model
require $root . '/app/models/Result.php'; // Result model
// Include layout helpers for rendering
require $root . '/app/views/partials/layout.php'; // Layout functions

// Instantiate models with shared PDO
$userModel = new User($pdo); // User operations
$olevelModel = new OLevel($pdo); // O-Level operations
$alevelModel = new ALevel($pdo); // A-Level operations
$resultModel = new Result($pdo); // Result operations

// Define compulsory O-Level subjects
$compulsoryOLevel = ['English', 'Mathematics', 'Physics', 'Chemistry', 'History', 'Geography', 'Biology']; // Required O-Level list
// Map of O-Level grades to buckets and weights
$olevelGradeMap = [ // Grade to weight map
    'D1' => ['bucket' => 'distinction', 'weight' => 0.3],
    'D2' => ['bucket' => 'distinction', 'weight' => 0.3],
    'C3' => ['bucket' => 'credit', 'weight' => 0.2],
    'C4' => ['bucket' => 'credit', 'weight' => 0.2],
    'C5' => ['bucket' => 'credit', 'weight' => 0.2],
    'C6' => ['bucket' => 'credit', 'weight' => 0.2],
    'P7' => ['bucket' => 'pass', 'weight' => 0.1],
    'P8' => ['bucket' => 'pass', 'weight' => 0.1],
    'F9' => ['bucket' => 'fail', 'weight' => 0.0],
];
// Map of A-Level principle grades to points
$alevelPrinciplePoints = ['A' => 6, 'B' => 5, 'C' => 4, 'D' => 3, 'E' => 2, 'O' => 1, 'F' => 0]; // Principle points
// Map of A-Level subsidiary grades to points
$alevelSubsidiaryPoints = ['D1' => 1, 'D2' => 1, 'C3' => 1, 'C4' => 1, 'C5' => 1, 'C6' => 1, 'P7' => 0, 'P8' => 0, 'F9' => 0]; // Subsidiary points

// Require authentication for all actions
if (!isset($_SESSION['user_id'])) { // If no user in session
    header('Location: /Charm/index.html'); // Redirect to landing (auth required)
    exit; // Stop script
}

// Get current user id and username
$userId = $_SESSION['user_id']; // Logged in user id
$username = $_SESSION['username'] ?? 'User'; // Friendly name
$gender = $_SESSION['gender'] ?? ''; // Gender for bonus

// Capture requested action
$action = $_GET['action'] ?? 'dashboard'; // Default action
// Initialize message bag
$message = $_GET['msg'] ?? ''; // Message text (supports redirected info)
$messageType = $message ? 'success' : ''; // Default type for redirected info

// Handle O-Level subjects submission
if ($action === 'olevel_subjects' && $_SERVER['REQUEST_METHOD'] === 'POST') { // If saving O-Level subjects
    $opt1 = trim($_POST['optional1'] ?? ''); // Optional 1
    $opt2 = trim($_POST['optional2'] ?? ''); // Optional 2
    $opt3 = trim($_POST['optional3'] ?? ''); // Optional 3
    $optionals = array_values(array_filter([$opt1, $opt2, $opt3], fn($s) => $s !== '')); // Filter non-empty
    if (count($optionals) < 2 || count($optionals) > 3) { // Validate count
        $message = 'Provide 2 or 3 optional subjects.'; // Error text
        $messageType = 'error'; // Error style
    } else { // Valid selection
        $olevelModel->saveSubjects($userId, $compulsoryOLevel, $optionals); // Save subjects
        $message = 'O-Level subjects saved.'; // Success text
        $messageType = 'success'; // Success style
    }
}

// Handle O-Level scores submission
if ($action === 'olevel_scores' && $_SERVER['REQUEST_METHOD'] === 'POST') { // If saving O-Level scores
    $grades = $_POST['grade'] ?? []; // Submitted grades
    $summary = $olevelModel->saveScores($userId, $grades, $olevelGradeMap); // Save scores and compute
    $resultModel->updateOLevel($userId, $summary['weight']); // Persist O-Level weight
    // Redirecting to A-Level subjects after O-Level save
    header('Location: /Charm/app/controllers/DashboardController.php?action=alevel_subjects&msg=O-Level scores saved'); // Move to next step
    exit; // Stop script after redirect
}

// Handle A-Level subjects submission
if ($action === 'alevel_subjects' && $_SERVER['REQUEST_METHOD'] === 'POST') { // If saving A-Level subjects
    $p1 = trim($_POST['principle1'] ?? ''); // Principle 1
    $p2 = trim($_POST['principle2'] ?? ''); // Principle 2
    $p3 = trim($_POST['principle3'] ?? ''); // Principle 3
    $subs = trim($_POST['subsidiary_choice'] ?? ''); // Subsidiary choice
    if ($p1 === '' || $p2 === '' || $p3 === '' || $subs === '') { // Validate presence
        $message = 'Enter all 3 principles and choose a subsidiary.'; // Error message
        $messageType = 'error'; // Error style
    } else { // Valid input
        $alevelModel->saveSubjects($userId, [$p1, $p2, $p3], $subs); // Save subjects
        $message = 'A-Level subjects saved.'; // Success message
        $messageType = 'success'; // Success style
    }
}

// Handle A-Level scores submission
if ($action === 'alevel_scores' && $_SERVER['REQUEST_METHOD'] === 'POST') { // If saving A-Level scores
    $grades = $_POST['grade'] ?? []; // Submitted grades
    $categories = $_POST['category'] ?? []; // Subject categories
    $totalPoints = $alevelModel->saveScores($userId, $grades, $categories, $alevelPrinciplePoints, $alevelSubsidiaryPoints); // Save scores
    $resultModel->updateTotalPoints($userId, $totalPoints); // Persist raw points
    // Redirecting to weight computation after A-Level scores
    header('Location: /Charm/app/controllers/DashboardController.php?action=weight&msg=A-Level scores saved'); // Next step
    exit; // Stop script after redirect
}

// Handle weight calculation submission
if ($action === 'weight' && $_SERVER['REQUEST_METHOD'] === 'POST') { // If computing weight
    $essential1 = $_POST['essential1'] ?? ''; // First essential
    $essential2 = $_POST['essential2'] ?? ''; // Second essential
    $desirable = $_POST['desirable'] ?? ''; // Desirable
    $cutoff = isset($_POST['cutoff']) && $_POST['cutoff'] !== '' ? floatval($_POST['cutoff']) : null; // Cutoff

    // Prevent duplicate essentials/desirable
    if ($essential1 === '' || $essential2 === '' || $desirable === '') { // Validate selection
        $message = 'Select two essentials and one desirable.'; // Error text
        $messageType = 'error'; // Style
    } elseif (count(array_unique([$essential1, $essential2, $desirable])) < 3) { // Check uniqueness
        $message = 'Essentials and desirable must be different subjects.'; // Error
        $messageType = 'error'; // Style
    } else { // Compute weights
        $scoresStmt = $pdo->prepare('SELECT subject_name, points, category FROM alevel_scores WHERE user_id = :uid'); // Query A-Level scores
        $scoresStmt->execute([':uid' => $userId]); // Execute query
        $rows = $scoresStmt->fetchAll(); // Fetch rows
        $pointsMap = []; // Map subjects to points
        foreach ($rows as $r) { $pointsMap[$r['subject_name']] = ['points' => $r['points'], 'category' => $r['category']]; } // Build map

        // Compute A-Level weighted score
        $alevelWeight = 0; // Initialize weight
        if (isset($pointsMap[$essential1])) { $alevelWeight += $pointsMap[$essential1]['points'] * 3; } // Essential 1
        if (isset($pointsMap[$essential2])) { $alevelWeight += $pointsMap[$essential2]['points'] * 3; } // Essential 2
        if (isset($pointsMap[$desirable])) { $alevelWeight += $pointsMap[$desirable]['points'] * 2; } // Desirable
        foreach ($rows as $r) { if ($r['category'] === 'subsidiary') { $alevelWeight += $r['points']; } } // Subsidiaries

        // Fetch stored O-Level weight
        $olevelWeightRow = $resultModel->getByUser($userId); // Get result row
        $olevelWeight = $olevelWeightRow['olevel_weight'] ?? 0; // Default 0
        // Gender bonus based on stored gender
        $genderBonus = ($gender === 'female') ? 1.5 : 0; // Bonus rule
        // Compute total weight
        $total = $alevelWeight + $olevelWeight + $genderBonus; // Final sum
        // Determine eligibility if cutoff provided
        $eligibility = null; // Default null
        if ($cutoff !== null && $cutoff > 0) { // If cutoff entered
            $eligibility = ($total >= $cutoff) ? 'Eligible' : 'Try another course'; // Eligibility text
        }
        // Save results
        $resultModel->save($userId, $olevelWeight, $alevelWeight, $genderBonus, $cutoff, $total, intval(array_sum(array_column($rows, 'points'))), $eligibility); // Persist
        $message = 'Weights computed. A-Level: ' . $alevelWeight . ' O-Level: ' . $olevelWeight . ' Bonus: ' . $genderBonus . ' Total: ' . $total . ($eligibility ? ' - ' . $eligibility : ''); // Feedback
        $messageType = 'success'; // Style
    }
}

// Enforce prerequisite: require A-Level scores before weight view access
if ($action === 'weight' && $_SERVER['REQUEST_METHOD'] !== 'POST') { // On GET weight
    $scoreCountStmt = $pdo->prepare('SELECT COUNT(*) as cnt FROM alevel_scores WHERE user_id = :uid'); // Count scores
    $scoreCountStmt->execute([':uid' => $userId]); // Execute count
    $countRow = $scoreCountStmt->fetch(); // Fetch result
    if (($countRow['cnt'] ?? 0) == 0) { // If no scores saved
        header('Location: /Charm/app/controllers/DashboardController.php?action=dashboard&msg=Save A-Level scores before computing weight'); // Redirect to dashboard with info
        exit; // Stop script after redirect
    }
}

// Prepare data for views
$viewData = []; // Container for view variables

// Load data per action
if ($action === 'olevel_scores') { // For O-Level scores view
    $viewData['subjects'] = $olevelModel->getSubjects($userId); // Subjects list
}
if ($action === 'alevel_scores') { // For A-Level scores view
    $viewData['subjects'] = $alevelModel->getSubjects($userId); // Subjects list
}
if ($action === 'weight') { // For weight view
    // Load A-Level subjects and corresponding points so the view can auto-select best two principles
    $subjects = $alevelModel->getSubjects($userId); // list of subjects with category
    $scores = $alevelModel->getScores($userId); // scores include points per subject
    // Build points map by subject name
    $pointsMap = [];
    foreach ($scores as $sc) {
        $pointsMap[$sc['subject_name']] = (int)($sc['points'] ?? 0);
    }
    // Attach points to the subjects array for view consumption
    foreach ($subjects as &$s) {
        $s['points'] = $pointsMap[$s['subject_name']] ?? 0;
    }
    unset($s);
    $viewData['subjects'] = $subjects; // Subjects with points
    $viewData['results'] = $resultModel->getByUser($userId); // Existing results
}
if ($action === 'view') { // For saved data view
    $viewData['user'] = $userModel->findById($userId); // User profile
    $viewData['olevelSubjects'] = $olevelModel->getSubjects($userId); // O-Level subjects
    $viewData['olevelScores'] = $olevelModel->getScores($userId); // O-Level scores
    $viewData['alevelSubjects'] = $alevelModel->getSubjects($userId); // A-Level subjects
    $viewData['alevelScores'] = $alevelModel->getScores($userId); // A-Level scores
    $viewData['results'] = $resultModel->getByUser($userId); // Results row
}

// Render layout
render_header(ucfirst($action)); // Header
render_sidebar($action); // Sidebar
open_content(ucfirst($action)); // Content start

// Show message if present
if ($message !== '') { echo '<div class="message ' . $messageType . '">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>'; } // Message box

// Resolve view file path
$viewPath = $root . '/app/views/' . $action . '.php'; // View path
if (file_exists($viewPath)) { // If view exists
    include $viewPath; // Render view
} else { // Missing view fallback
    echo '<p>View not found.</p>'; // Fallback text
}

// Render footer
render_footer(); // Footer and closing tags
