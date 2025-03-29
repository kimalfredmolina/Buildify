<?php
session_start();
include '/Buildify/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $_POST['project_name'];
    $stmt = $pdo->prepare("INSERT INTO projects (user_id, name) VALUES (?, ?)");
    $stmt->execute([1, $projectName]);
    header("Location: /php/Project.php?id=" . $pdo->lastInsertId());
    exit();
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
    <div class="container w-full md:max-w-3xl mx-auto pt-20">
        <div class="w-full px-4 md:px-6 text-xl text-gray-800 leading-normal">
            <div class="font-sans font-bold break-normal pt-6 pb-2 text-center border-b-2 border-gray-200">
                <p>Create New Project</p>
            </div>
            <form action="" method="POST" class="py-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="project_name">
                        Project Name
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="project_name" type="text" placeholder="Enter project name" name="project_name" required>
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>