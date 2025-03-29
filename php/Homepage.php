<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildiFy - Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body class="flex flex-col items-center min-h-screen bg-white">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center p-5 bg-slate-800 shadow-md rounded-b-lg">
        <div class="text-4xl ml-24 font-bold text-white">BuildiFy</div>
        <div class="mr-12 flex space-x-12 text-white">
            <a href="/php/Create.php" class="text-xl font-medium hover:text-blue-600 transition-colors duration-300">Create New Project</a>
            <a href="/Logout.php" class="text-xl font-medium hover:text-blue-600 transition-colors duration-300">Logout</a>
        </div>
    </nav>

    <!-- Welcome Message -->
    <div class="mt-32 text-center">
        <?php
        session_start();
        include '/Buildify/config.php';

        if (isset($_SESSION['name'])) {
            $userName = $_SESSION['name'];
            echo "<h1 class='text-2xl font-bold'>Welcome! <span class='user-name'>$userName</span> Build a new project now!</h1>";
        } else {
            echo "<h1 class='text-2xl font-bold'>Welcome! Guest Build a new project now!</h1>";
        }
        ?>
    </div>

    <!-- Project Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-10 px-10">
        <div class="bg-gray-200 rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow duration-300">
            <h2 class="text-lg font-semibold">Project 1</h2>
            <div class="text-right">
                <a href="#" class="text-blue-500 text-xl">&rarr;</a>
            </div>
        </div>
        <div class="bg-gray-200 rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow duration-300">
            <h2 class="text-lg font-semibold">Project 1</h2>
            <div class="text-right">
                <a href="#" class="text-blue-500 text-xl">&rarr;</a>
            </div>
        </div>
        <div class="bg-gray-200 rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow duration-300">
            <h2 class="text-lg font-semibold">Project 1</h2>
            <div class="text-right">
                <a href="#" class="text-blue-500 text-xl">&rarr;</a>
            </div>
        </div>
    </div>
</body>

</html>