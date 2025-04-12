<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildiFy - Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="icon" type="image/png" href="/images/buildifylogo2.png">
</head>

<body class="flex flex-col items-center min-h-screen bg-white">
    <nav class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center p-5 bg-slate-800 shadow-md rounded-b-lg">
        <div class="text-4xl ml-24 font-bold bg-gradient-to-r from-yellow-400 via-rose-700 to-lime-500 bg-clip-text text-transparent">BuildiFy</div>
        <div class="mr-12 flex space-x-12 text-white">
            <a href="/php/Create.php" class="text-xl font-medium hover:text-blue-600 transition-colors duration-300">Create New Project</a>
            <a href="/Logout.php" class="text-xl font-medium hover:text-blue-600 transition-colors duration-300">Logout</a>
        </div>
    </nav>

    <div class="mt-32 text-center">
        <?php
        session_start();
        include('../config.php');

        if (isset($_SESSION['name'])) {
            $userName = $_SESSION['name'];
            echo "<h1 class='text-2xl font-bold'>Welcome! <span class='user-name'>$userName</span> Build a new CMS project now!</h1>";
        } else {
            echo "<h1 class='text-2xl font-bold'>Welcome! Guest Build a new CMS project now!</h1>";
        }
        ?>
    </div>

    <div class="mt-10 px-10 w-full max-w-4xl">
        <h2 class="text-xl font-semibold mb-4">Your Projects</h2>
        <ul class="space-y-4">
            <?php

            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
                $deleteId = $_POST['delete_id'];
                
                $deleteBlocks = $conn->prepare("DELETE FROM project_blocks WHERE project_id = ?");
                $deleteBlocks->bind_param("i", $deleteId);
                $deleteBlocks->execute();
                
                $stmt = $conn->prepare("DELETE FROM projects WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $deleteId, $_SESSION['user_id']);
                $stmt->execute();
                
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];

                $query = "SELECT * FROM projects WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $projectName = htmlspecialchars($row['project_name']);
                        $createdAt = $row['created_at'];
                        echo "
                        <li class='p-4 bg-gray-100 rounded shadow-sm flex justify-between items-center'>
                            <div>
                                <strong>Project:</strong> $projectName <br>
                                <strong>Created At:</strong> $createdAt
                            </div>
                            <div class='flex space-x-2'>
                                <a href='/php/Project.php?id={$row['id']}' class='px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm'>Edit</a>
                                <form action='' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this project?\");'>
                                    <input type='hidden' name='delete_id' value='{$row['id']}'>
                                    <button type='submit' class='px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm'>Delete</button>
                                </form>
                            </div>
                        </li>";
                    }
                } else {
                    echo "<li class='text-gray-500'>You have no projects yet.</li>";
                }
            } else {
                echo "<li class='text-red-500'>Please log in to view your projects.</li>";
            }
            ?>
        </ul>
    </div>
</body>

</html>