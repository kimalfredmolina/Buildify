<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildiFy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="/images/buildifylogo2.png">
</head>

<body class="flex flex-col min-h-screen bg-white">
    <div class="absolute top-0 z-[-2] h-screen w-screen bg-[#000000] bg-[radial-gradient(#ffffff33_1px,#00091d_1px)] bg-[size:20px_20px]"></div>

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center p-5 bg-slate-800 shadow-md rounded-b-lg">
        <div class="text-4xl ml-24 font-bold text-white">BuildiFy</div>
        <div class="mr-12 flex space-x-12 text-white">
            <a href="#about" class="text-xl font-medium hover:text-blue-600 transition-colors duration-300">About Us</a>
            <a href="/contact-us.html" class="text-xl font-medium hover:text-blue-600 transition-colors duration-300">Contact Us</a>
            <a href="/php/Signin.php" class="text-xl font-medium hover:text-blue-600 transition-colors duration-300">Login</a>
            <a href="/php/Signup.php" class="text-xl font-medium hover:text-blue-600 transition-colors duration-300">Signup</a>
        </div>
    </nav>

    <!-- Main Content Section -->
    <main class="flex-grow flex items-center justify-center h-screen">
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
    </main>

    <div id="about" class="relative flex min-h-screen flex-col justify-center bg-slate-800 overflow-hidden py-6 sm:py-12">
        <div class="w-full items-center mx-auto max-w-screen-lg">
            <div class="group grid w-full grid-cols-2">
                <div>
                    <div class="pr-12">
                        <p class="peer mb-6 text-gray-400">
                            <strong>BuildiFy</strong> is a free, user-friendly CMS platform that allows anyone to create, customize, and deploy websites effortlessly.
                            With BuildiFy, you can edit headers, footers, colors, and layouts, upload media, and preview changes in real-time—no coding required.
                        </p>
                        <p class="mb-6 text-gray-400">
                            Sign up today and start building your website instantly. Whether you're creating a blog, portfolio, or business site,
                            BuildiFy gives you the tools to bring your vision to life without technical barriers.
                        </p>
                        <h3 class="mb-4 font-semibold text-xl text-gray-400">Why Choose BuildiFy?</h3>
                        <ul role="list" class="marker:text-sky-400 list-disc pl-5 space-y-3 text-slate-500">
                            <li>Easy-to-use, no coding required</li>
                            <li>Fully customizable design options</li>
                            <li>Fast and free website deployment</li>
                        </ul>
                    </div>
                </div>
                <div class="pr-16 relative flex flex-col before:block before:absolute before:h-1/6 before:w-4 before:bg-blue-500 before:bottom-0 before:right-0 before:rounded-lg before:transition-all group-hover:before:bg-orange-300 overflow-hidden">
                    <div class="absolute top-0 right-0 bg-blue-500 w-4/6 px-12 py-14 flex flex-col justify-center rounded-xl group-hover:bg-sky-600 transition-all">
                        <span class="block mb-10 font-bold group-hover:text-orange-300">JOIN BUILDIFY</span>
                        <h2 class="text-white font-bold text-3xl">
                            Get started for free and create your CMS-powered website today!
                        </h2>
                    </div>
                    <a class="font-bold text-sm flex mt-2 mb-8 items-center gap-2 text-white" href="/php/Signup.php">
                        <span>SIGN UP NOW</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    <div class="rounded-xl overflow-hidden">
                        <img src="/images/cms.png" alt="BuildiFy CMS">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-700 w-auto py-8">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Solutions</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-white hover:text-blue-300">Marketing</a></li>
                        <li><a href="#" class="text-white hover:text-blue-300">Analytics</a></li>
                        <li><a href="#" class="text-white hover:text-blue-300">Commerce</a></li>
                        <li><a href="#" class="text-white hover:text-blue-300">Insights</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-white hover:text-blue-300">Pricing</a></li>
                        <li><a href="#" class="text-white hover:text-blue-300">Documentation</a></li>
                        <li><a href="#" class="text-white hover:text-blue-300">Guides</a></li>
                        <li><a href="#" class="text-white hover:text-blue-300">API Status</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-white hover:text-blue-300">About</a></li>
                        <li><a href="#" class="text-white hover:text-blue-300">Blog</a></li>
                        <li><a href="#" class="text-white hover:text-blue-300">Jobs</a></li>
                        <li><a href="#" class="text-white hover:text-blue-300">Press</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-white hover:text-blue-300">Claim</a></li>
                        <li><a href="#" class="text-white hover:text-blue-300">Privacy</a></li>
                        <li><a href="#" class="text-white hover:text-blue-300">Terms</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200 text-center">
                <p class="text-white font-bold">© 2025 CBZ Inc. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>