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
        <!-- Left Section -->
        <div class="md:w-1/2 p-8 flex flex-col justify-center bg-slate-800">
            <h1 class="text-5xl font-bold mb-6 text-white animate-fade-in">BuildiFy</h1>
            <p class="text-lg text-white leading-relaxed animate-slide-in">
                BuildiFy is a user-friendly platform that lets you create and customize websites effortlessly. Edit headers, footers, colors, and layouts, upload media, and preview changes in real-time—no coding required. Build your perfect website today!
            </p>
        </div>
        <!-- Right Section -->
        <div class="md:w-1/2 p-10">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center animate-pop-in">Sign Up</h2>
            <form class="space-y-6">
                <div>
                    <label for="email" class="block text-base font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" placeholder="Email@gmail.com" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-pink-500 focus:border-pink-500 animate-fade-in">
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
                <button type="submit" class="w-full bg-pink-500 text-white py-3 px-6 rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50 animate-pop-in">
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