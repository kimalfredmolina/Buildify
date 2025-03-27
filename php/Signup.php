<?php
session_start();
include '/Buildify/config.php';

if (isset($_POST['register'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['retypepassword']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    if (!empty($email) && !empty($password) && !empty($confirm_password) && !empty($name)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format!';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match!';
        } else {
            $check_email_query = "SELECT * FROM user_account WHERE email = '$email'";
            $check_email_result = mysqli_query($conn, $check_email_query);

            if (mysqli_num_rows($check_email_result) > 0) {
                $error = 'Email is already registered!';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insert_query = "INSERT INTO user_account (email, password, name) VALUES ('$email', '$hashed_password', '$name')";

                if (mysqli_query($conn, $insert_query)) {
                    $_SESSION['success'] = 'Account created successfully! You can now sign in.';
                    header('Location: /php/Signin.php');
                    exit();
                } else {
                    $error = 'Failed to register. Please try again later!';
                }
            }
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
    <title>Sign Up - BuildiFy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="absolute inset-0 -z-10 h-full w-full items-center px-5 py-24 [background:radial-gradient(125%_125%_at_50%_10%,#000_40%,#63e_100%)]"></div>
    <div class="flex flex-col md:flex-row bg-white rounded-lg shadow-lg max-w-5xl w-full overflow-hidden">
        <div class="md:w-1/2 p-8 flex flex-col justify-center bg-slate-800">
            <h1 class="text-5xl font-bold mb-6 text-white">BuildiFy</h1>
            <p class="text-lg text-white leading-relaxed">
                BuildiFy is a user-friendly platform that lets you create and customize websites effortlessly. Edit headers, footers, colors, and layouts, upload media, and preview changes in real-time—no coding required. Build your perfect website today!
            </p>
        </div>
        <div class="md:w-1/2 p-10">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Sign Up</h2>
            <?php if (isset($error)): ?>
                <p class="text-red-500 text-center mb-4"><?= $error; ?></p>
            <?php endif; ?>
            <form method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-base font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" placeholder="Email@gmail.com" required
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                </div>
                <div>
                    <label for="name" class="block text-base font-medium text-gray-700">Name</label>
                    <input id="name" name="name" type="text" placeholder="Your Name" required
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                </div>
                <div class="relative">
                    <label for="password" class="block text-base font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" placeholder="Password" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-pink-500 focus:border-pink-500 animate-fade-in">
                    <img id="togglePassword" src="/images/openeye.png" alt="Toggle Password Visibility" class="absolute bottom-0.5 right-3 w-6 h-6 transform -translate-y-1/2 cursor-pointer" onclick="togglePasswordVisibility('password', 'togglePassword')">
                </div>
                <div class="relative">
                    <label for="retypepassword" class="block text-base font-medium text-gray-700">Confirm Password</label>
                    <input id="retypepassword" name="retypepassword" type="password" placeholder="Retype Password" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-pink-500 focus:border-pink-500 animate-fade-in">
                    <img id="toggleRetypePassword" src="/images/openeye.png" alt="Toggle Password Visibility" class="absolute bottom-0.5 right-3 w-6 h-6 transform -translate-y-1/2 cursor-pointer" onclick="togglePasswordVisibility('retypepassword', 'toggleRetypePassword')">
                </div>
                <button type="submit" name="register" class="w-full bg-pink-500 text-white py-3 px-6 rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50">
                    Create Account
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
                Already Have an Account? <a href="/php/Signin.php" class="text-pink-500 hover:underline">Sign In</a>
            </p>
        </div>
    </div>
    <script src="/js/signup.js"></script>
</body>

</html>