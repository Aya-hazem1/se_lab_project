<?php

// Basic autoloader to load classes from the src directory
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Controllers\TaskController;

$controller = new TaskController();

// --- Handle task creation (FR1) ---
// In a real app, this data would come from a form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_task'])) {
    $title = !empty($_POST['title']) ? htmlspecialchars($_POST['title']) : 'Untitled Task';
    $description = !empty($_POST['description']) ? htmlspecialchars($_POST['description']) : '';
    $deadline = !empty($_POST['deadline']) ? htmlspecialchars($_POST['deadline']) : date('Y-m-d');
    $priority = !empty($_POST['priority']) ? htmlspecialchars($_POST['priority']) : 'Medium';
    
    $controller->createTask($title, $description, $deadline, $priority);
    
    // Redirect to the same page to prevent form resubmission
    header("Location: index.php");
    exit();
}

// --- Fetch and display tasks (FR4, FR7, FR8) ---
$statusFilter = $_GET['status'] ?? null;
$priorityFilter = $_GET['priority'] ?? null;

$filteredTasks = $controller->getFilteredTasks($statusFilter, $priorityFilter);

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منظم المهام الذكي</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #4a4a4a; }
        form { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        form input, form select, form button { display: block; width: 95%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }
        form button { background-color: #007bff; color: white; border: none; cursor: pointer; }
        .task-card { border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 10px; background: #fafafa; }
        .task-card h3 { margin-top: 0; }
        .task-card .meta { font-size: 0.9em; color: #666; }
        .priority-High { border-left: 5px solid #dc3545; }
        .priority-Medium { border-left: 5px solid #ffc107; }
        .priority-Low { border-left: 5px solid #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <h1>منظم المهام الذكي</h1>

        <!-- Form for creating a new task -->
        <h2>إنشاء مهمة جديدة</h2>
        <form method="POST" action="index.php">
            <input type="text" name="title" placeholder="عنوان المهمة" required>
            <input type="text" name="description" placeholder="وصف المهمة">
            <input type="date" name="deadline" required>
            <select name="priority">
                <option value="High">أولوية عالية</option>
                <option value="Medium" selected>أولوية متوسطة</option>
                <option value="Low">أولوية منخفضة</option>
            </select>
            <button type="submit" name="create_task">إنشاء المهمة</button>
        </form>

        <!-- Display tasks -->
        <h2>قائمة المهام</h2>
        <?php if (empty($filteredTasks)): ?>
            <p>لا توجد مهام لعرضها.</p>
        <?php else: ?>
            <?php foreach ($filteredTasks as $task): ?>
                <div class="task-card priority-<?php echo htmlspecialchars($task['priority']); ?>">
                    <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                    <p><?php echo htmlspecialchars($task['description']); ?></p>
                    <div class="meta">
                        <span><strong>الموعد النهائي:</strong> <?php echo htmlspecialchars($task['deadline']); ?></span> |
                        <span><strong>الأولوية:</strong> <?php echo htmlspecialchars($task['priority']); ?></span> |
                        <span><strong>الحالة:</strong> <?php echo htmlspecialchars($task['status']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
