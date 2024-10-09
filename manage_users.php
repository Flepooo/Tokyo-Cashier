<?php
// Start session
session_start();

// Check if user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Database connection
$host = 'localhost';
$dbname = 'tokyo_pos'; // Database name
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Adding a new user
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $password = $_POST['password']; // Plain password, you can change this to hash
        $sql = "INSERT INTO users (username, password, email, role) VALUES ('$username', '$password', '$email', '$role')";
        $conn->query($sql);
    }

    // Deleting a user
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $sql = "DELETE FROM users WHERE user_id = $user_id";
        $conn->query($sql);
    }
}

// Fetch all users
$users = $conn->query("SELECT * FROM users");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Tokyo POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-4xl">
            <h1 class="text-2xl font-bold mb-6 text-center">Manage Users</h1>
            <!-- Add New User -->
            <form action="manage_users.php" method="POST" class="mb-6">
                <h2 class="text-xl mb-4">Add New User</h2>
                <div class="grid gap-4 grid-cols-4">
                    <input type="text" name="username" placeholder="Username" required class="p-2 border rounded">
                    <input type="email" name="email" placeholder="Email" required class="p-2 border rounded">
                    <input type="password" name="password" placeholder="Password" required class="p-2 border rounded">
                    <select name="role" required class="p-2 border rounded">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" name="add_user"
                    class="mt-4 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">Add User</button>
            </form>

            <!-- Display All Users -->
            <h2 class="text-xl mb-4">User List</h2>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-4">Username</th>
                        <th class="py-2 px-4">Email</th>
                        <th class="py-2 px-4">Role</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td class="py-2 px-4"><?php echo $user['username']; ?></td>
                        <td class="py-2 px-4"><?php echo $user['email']; ?></td>
                        <td class="py-2 px-4"><?php echo $user['role']; ?></td>
                        <td class="py-2 px-4">
                            <!-- Delete User -->
                            <form action="manage_users.php" method="POST" class="inline">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <button type="submit" name="delete_user"
                                    class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Button to go back to index.php -->
            <div class="mt-6 text-center">
                <a href="index.php" class="bg-gray-300 text-black py-2 px-4 rounded hover:bg-gray-400">Back to Home</a>
            </div>

        </div>
    </div>
</body>

</html>