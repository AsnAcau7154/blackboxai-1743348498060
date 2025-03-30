<?php
require_once 'backend/db.php';
require_once 'backend/user-auth.php';

// Test database connection
echo "<h2>Database Connection Test</h2>";
try {
    $testQuery = $pdo->query("SELECT 1");
    echo "<p style='color:green'>✓ Database connection successful</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit();
}

// Test admin user exists
echo "<h2>Admin User Test</h2>";
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin' AND role = 'admin'");
$stmt->execute();
$admin = $stmt->fetch();

if ($admin) {
    echo "<p style='color:green'>✓ Admin user exists</p>";
    echo "<pre>Username: admin\nPassword: Admin@123</pre>";
    
    // Test admin login
    $_POST = [
        'username' => 'admin',
        'password' => 'Admin@123',
        'login' => true
    ];
    
    ob_start();
    include 'backend/user-auth.php';
    ob_end_clean();
    
    if (isset($_SESSION['user_id']) && $_SESSION['username'] === 'admin') {
        echo "<p style='color:green'>✓ Admin login successful</p>";
        session_unset();
    } else {
        echo "<p style='color:red'>✗ Admin login failed</p>";
    }
} else {
    echo "<p style='color:red'>✗ Admin user not found</p>";
}

// Test teacher user exists
echo "<h2>Teacher User Test</h2>";
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'teacher1' AND role = 'teacher'");
$stmt->execute();
$teacher = $stmt->fetch();

if ($teacher) {
    echo "<p style='color:green'>✓ Teacher user exists</p>";
    echo "<pre>Username: teacher1\nPassword: Teacher@123</pre>";
    
    // Test teacher login
    $_POST = [
        'username' => 'teacher1',
        'password' => 'Teacher@123',
        'login' => true
    ];
    
    ob_start();
    include 'backend/user-auth.php';
    ob_end_clean();
    
    if (isset($_SESSION['user_id']) && $_SESSION['username'] === 'teacher1') {
        echo "<p style='color:green'>✓ Teacher login successful</p>";
        session_unset();
    } else {
        echo "<p style='color:red'>✗ Teacher login failed</p>";
    }
} else {
    echo "<p style='color:red'>✗ Teacher user not found</p>";
}

// Test database tables
echo "<h2>Database Structure Test</h2>";
$requiredTables = ['users', 'students', 'teachers', 'classes', 'subjects', 'attendance', 'grades', 'fees'];
$allTablesExist = true;

foreach ($requiredTables as $table) {
    try {
        $pdo->query("SELECT 1 FROM $table LIMIT 1");
        echo "<p style='color:green'>✓ Table $table exists</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red'>✗ Table $table missing</p>";
        $allTablesExist = false;
    }
}

if ($allTablesExist) {
    echo "<p style='color:green'>✓ All required tables exist</p>";
} else {
    echo "<p style='color:red'>✗ Some tables are missing</p>";
}

// Test login page accessibility
echo "<h2>Login Page Test</h2>";
$loginPage = file_get_contents('login.html');
if (strpos($loginPage, '<form action="/backend/user-auth.php"') !== false) {
    echo "<p style='color:green'>✓ Login page found with correct form action</p>";
    echo "<p><a href='/login.html' target='_blank'>Open Login Page</a></p>";
} else {
    echo "<p style='color:red'>✗ Login page not found or has incorrect form</p>";
}

// Test dashboard redirects
echo "<h2>Dashboard Access Test</h2>";
echo "<p>Try accessing these after login:</p>";
echo "<ul>";
echo "<li><a href='/pages/admin/dashboard.html' target='_blank'>Admin Dashboard</a></li>";
echo "<li><a href='/pages/teacher/dashboard.html' target='_blank'>Teacher Dashboard</a></li>";
echo "<li><a href='/pages/student/dashboard.html' target='_blank'>Student Dashboard</a></li>";
echo "<li><a href='/pages/parent/dashboard.html' target='_blank'>Parent Dashboard</a></li>";
echo "</ul>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Visit <a href='/backend/init-db.php' target='_blank'>/backend/init-db.php</a> to initialize database</li>";
echo "<li>Test login at <a href='/login.html' target='_blank'>/login.html</a></li>";
echo "<li>Check admin dashboard functionality</li>";
echo "</ol>";
?>