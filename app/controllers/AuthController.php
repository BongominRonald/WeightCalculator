<?php // Handles signup, login, and logout actions
// Resolve project root path
$root = dirname(__DIR__, 2); // Move up to project root
// Bring in database connection
require $root . '/config/db_connect.php'; // Shared PDO
// Load User model
require $root . '/app/models/User.php'; // User model class

// Instantiate User model
$userModel = new User($pdo); // Model for user operations

// Capture action from query string
$action = $_GET['action'] ?? 'login'; // Default to login

// Handle logout early
if ($action === 'logout') { // If logout requested
    session_destroy(); // Clear session
    header('Location: /Charm/index.html'); // Redirecting to landing after logout
    exit; // Stop script
}

// Helper to validate password complexity
function is_valid_password(string $password): bool {
    // Must be at least 6 chars with letters and numbers
    return strlen($password) >= 6 && preg_match('/[A-Za-z]/', $password) && preg_match('/\d/', $password); // Complexity rule
}

// Handle signup submission
if ($action === 'signup' && $_SERVER['REQUEST_METHOD'] === 'POST') { // Signup form posted
    $username = trim($_POST['username'] ?? ''); // Get username
    $email = trim($_POST['email'] ?? ''); // Get email
    $password = $_POST['password'] ?? ''; // Get password
    $gender = $_POST['gender'] ?? ''; // Get gender

    // Basic validation
    if ($username === '' || $email === '' || $password === '' || $gender === '') { // Check required fields
        header('Location: /Charm/index.html?error=All fields required#signup'); // Redirecting signup due to missing fields
        exit; // Stop script
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validate email format
        header('Location: /Charm/index.html?error=Valid email required#signup'); // Redirecting signup due to invalid email
        exit; // Stop script
    }
    if (!is_valid_password($password)) { // Validate password rules
        header('Location: /Charm/index.html?error=Password must be 6+ chars with letters and numbers#signup'); // Redirecting signup due to weak password
        exit; // Stop script
    }

    try { // Attempt user creation
        $userModel->create($username, $email, $password, $gender); // Save user
        header('Location: /Charm/index.html?success=Account created. Please login#login'); // Redirecting to login after signup
        exit; // Stop script
    } catch (PDOException $e) { // Handle DB errors
        if ($e->getCode() === '23000') { // Duplicate email
            header('Location: /Charm/index.html?error=Email already registered#signup'); // Redirecting signup duplicate email
        } else { // Other error
            header('Location: /Charm/index.html?error=Signup failed#signup'); // Redirecting signup generic error
        }
        exit; // Stop script
    }
}

// Handle login submission
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') { // Login form posted
    $email = trim($_POST['email'] ?? ''); // Get email
    $password = $_POST['password'] ?? ''; // Get password

    // Validate presence
    if ($email === '' || $password === '') { // Missing fields
        header('Location: /Charm/index.html?error=Email and password required#login'); // Redirecting login due to missing fields
        exit; // Stop script
    }

    // Fetch user by email
    $user = $userModel->findByEmail($email); // Retrieve user row
    if ($user && password_verify($password, $user['password_hash'])) { // Check password
        $_SESSION['user_id'] = $user['id']; // Store user id
        $_SESSION['username'] = $user['username']; // Store username
        $_SESSION['gender'] = $user['gender']; // Store gender
        header('Location: /Charm/app/controllers/DashboardController.php?action=dashboard'); // Redirecting to dashboard after login
        exit; // Stop script
    }

    // Invalid credentials case
    header('Location: /Charm/index.html?error=Invalid credentials#login'); // Redirecting login due to invalid credentials
    exit; // Stop script
}

// Default fallback: redirect to landing
header('Location: /Charm/index.html'); // Redirecting fallback to landing
exit; // Stop script
