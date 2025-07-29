<?php
require 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['age'])) {
    try {
        $name = $conn->real_escape_string(trim($_POST['name']));
        $age = (int)$_POST['age'];
        
        if (empty($name) || $age <= 0) {
            throw new Exception("Invalid input data");
        }
        
        $stmt = $conn->prepare("INSERT INTO users (name, age) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $age);
        $stmt->execute();
        
        // Redirect to prevent form resubmission
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch users
$users = [];
try {
    $result = $conn->query("SELECT id, name, age, COALESCE(status, 0) as status FROM users");
    if ($result) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    $error_message = "Error fetching users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management</title>
 <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>User Form</h2>
    
    <?php if (!empty($error_message)): ?>
      <div class="alert error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
    <form method="post" action="">
      <div class="form-group">
        <input type="text" name="name" placeholder="Name" required minlength="2" maxlength="100">
      </div>
      <div class="form-group">
        <input type="number" name="age" placeholder="Age" required min="1" max="120">
      </div>
      <button type="submit" class="btn-submit">Add User</button>
    </form>

    <h2>Users List</h2>
    
    <?php if (empty($users)): ?>
      <p class="no-users">No users found</p>
    <?php else: ?>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Age</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr id="row-<?php echo htmlspecialchars($user['id']); ?>">
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['age']); ?></td>
                <td class="status" id="status-<?php echo htmlspecialchars($user['id']); ?>" data-status="<?php echo $user['status']; ?>">
                  <?php echo $user['status']; ?>
                </td>
                <td>
                  <button class="toggle" data-id="<?php echo htmlspecialchars($user['id']); ?>">Toggle</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <script src="script.js"></script>
</body>
</html>