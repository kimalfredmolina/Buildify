<?php
session_start();
include '/Buildify/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /php/Signin.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $projectName = $_POST['project_name'] ?? '';
        if (empty($projectName)) {
            throw new Exception("Project name is required");
        }

        $user_id = $_SESSION['user_id'];

        // Verify user exists
        $check_user = $conn->prepare("SELECT id FROM user_account WHERE id = ?");
        $check_user->bind_param("i", $user_id);
        $check_user->execute();
        if ($check_user->get_result()->num_rows === 0) {
            throw new Exception("User account doesn't exist. Please login again.");
        }

        // Insert project
        $stmt = $conn->prepare("INSERT INTO projects (user_id, project_name, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $user_id, $projectName);
        $stmt->execute();

        header("Location: /php/Project.php?id=" . $stmt->insert_id);
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Project</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="container mx-auto pt-20 max-w-lg">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-xl font-bold text-center border-b pb-2">Create New Project</h2>

            <?php if (!empty($error)) : ?>
                <p class="text-red-500 text-sm mt-2"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form action="" method="POST" class="mt-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="project_name">
                        Project Name
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="project_name"
                        type="text"
                        placeholder="Enter project name"
                        name="project_name"
                        required>
                </div>
                <div class="flex justify-center">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>