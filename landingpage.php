<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildiFy Landing Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg-white">
    <div class="absolute inset-0 -z-10 h-full w-full items-center px-5 py-24 [background:radial-gradient(125%_125%_at_50%_10%,#000_40%,#63e_100%)]"></div>
    <div class="text-center">
        <h1 class="text-7xl mr-40 font-medium text-white">Welcome to</h1>
        <h2 class="text-9xl font-bold text-white">BuildiFy</h2>
        <p class="mt-6 text-3xl text-white">Create Account now! for free</p>
        <button
            onclick="window.location.href='/php/Signup.php'"
            class="group relative mt-10">
            <div class="relative z-10 inline-flex h-12 items-center justify-center overflow-hidden rounded-md border border-neutral-200 bg-transparent px-16 font-medium text-white transition-all duration-300 group-hover:-translate-x-3 group-hover:-translate-y-3">
                Sign Up Now!
            </div>
            <div class="absolute inset-0 z-0 h-full w-full rounded-md transition-all duration-300 group-hover:-translate-x-3 group-hover:-translate-y-3 group-hover:[box-shadow:5px_5px_#a3a3a3,10px_10px_#d4d4d4,15px_15px_#e5e5e5]"></div>
        </button>
    </div>
</body>

</html>