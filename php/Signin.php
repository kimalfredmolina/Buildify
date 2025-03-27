<?php
session_start();
include '/Buildify/config.php';

$error = '';

if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
} else {
    $success_message = null;
}

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (!empty($email) && !empty($password)) {
        $query_email = "SELECT * FROM user_account WHERE Email = '$email'";
        $result_email = mysqli_query($conn, $query_email);

        if ($result_email && mysqli_num_rows($result_email) > 0) {
            $row_user = mysqli_fetch_assoc($result_email);

            if (isset($row_user['Password'])) {
                $hashed_password = $row_user['Password'];

                if (password_verify($password, $hashed_password)) {
                    $_SESSION['name'] = $row_user['Name'];
                    header('location: /php/Homepage.php');
                    exit();
                } else {
                    $error = 'Incorrect password!';
                }
            } else {
                $error = 'Password column not found in database row!';
            }
        } else {
            $error = 'Incorrect email!';
        }
    } else {
        $error = 'Please fill in all fields!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - BuildiFy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="absolute top-0 z-[-2] h-screen w-screen bg-[#000000] bg-[radial-gradient(#ffffff33_1px,#00091d_1px)] bg-[size:20px_20px]"></div>
    <div class="flex flex-col md:flex-row bg-white rounded-lg shadow-lg max-w-5xl w-full overflow-hidden">
        <!-- Left Section -->
        <div class="md:w-1/2 p-8 flex flex-col justify-center bg-slate-800">
            <h1 class="text-5xl font-bold mb-6 text-white animate-fade-in">BuildiFy</h1>
            <p class="text-lg text-white leading-relaxed animate-slide-in">
                BuildiFy is a user-friendly platform that lets you create and customize websites effortlessly. Edit headers, footers, colors, and layouts, upload media, and preview changes in real-time—no coding required. Build your perfect website today!
            </p>
        </div>
        <!-- Right Section -->
        <div class="md:w-1/2 p-10">
            <?php if ($success_message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline"><?= $success_message; ?></span>
                </div>
            <?php endif; ?>
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center animate-pop-in">Sign In</h2>
            <!-- Error Message -->
            <?php if (!empty($error)) : ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?= htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            <form action="/php/Signin.php" method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-base font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" placeholder="Email@gmail.com" required
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-pink-500 focus:border-pink-500 animate-fade-in">
                </div>
                <div class="relative">
                    <label for="password" class="block text-base font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" placeholder="Password" required
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-pink-500 focus:border-pink-500 animate-fade-in">
                    <img id="togglePasswordIcon" src="/images/openeye.png" alt="Toggle Password Visibility"
                        class="absolute bottom-0.5 right-3 w-6 h-6 transform -translate-y-1/2 cursor-pointer" onclick="togglePasswordVisibility()">
                </div>
                <button type="submit" name="submit" class="w-full bg-pink-500 text-white py-3 px-6 rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50 animate-pop-in">
                    Sign In
                </button>
            </form>
            <div class="flex items-center my-6">
                <div class="flex-grow h-px bg-gray-300"></div>
                <span class="px-4 text-base text-gray-700">OR</span>
                <div class="flex-grow h-px bg-gray-300"></div>
            </div>
            <button class="w-full flex items-center justify-center border border-gray-300 py-3 px-6 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50 animate-pop-in">
                <img src="/images/googleicon.webp" alt="Google" class="w-6 h-6 mr-2">
                Continue with Google
            </button>
            <p class="mt-6 text-center text-base text-gray-700">
                Don't Have an Account? <a href="/php/Signup.php" class="text-pink-500 hover:underline">Sign Up</a>
            </p>
        </div>
    </div>
    <script src="/js/signin.js"></script>
</body>

</html>