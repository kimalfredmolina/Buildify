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
            <a href="https://kim-alfred-portfolio.vercel.app/#contact" class="text-xl font-medium hover:text-blue-600 transition-colors duration-300">Contact Us</a>
            <a href="/php/Signin.php" class="text-xl font-medium hover:text-blue-600 transition-colors duration-300">Login</a>
            <a href="/php/Signup.php" class="text-xl font-medium hover:text-blue-600 transition-colors duration-300">Signup</a>
        </div>
    </nav>

    <!-- Main Content Section -->
    <main class="flex-grow flex items-center justify-center h-screen">
        <div class="text-center">
            <h1 class="text-7xl mr-40 font-medium text-white">Welcome to</h1>
            <h2 class="text-9xl font-bold bg-gradient-to-r from-yellow-400 via-rose-700 to-lime-500 bg-clip-text text-transparent">BuildiFy</h2>
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
    <footer class="bg-slate-700 w-auto py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <!-- Company Info & Social Links -->
                <div class="md:col-span-1">
                    <h3 class="text-3xl font-bold text-white mb-6">BuildiFy</h3>
                    <p class="text-gray-300 mb-6">Create and customize your website with our powerful CMS platform.</p>
                    <div class="flex space-x-4">
                        <a href="#" target="_blank" class="text-white hover:text-blue-400 transition-colors duration-300">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                        <a href="#" target="_blank" class="text-white hover:text-pink-400 transition-colors duration-300">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                        <a href="https://github.com/kimalfredmolina" target="_blank" class="text-white hover:text-gray-400 transition-colors duration-300">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-white">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-blue-300 transition-colors duration-300">Home</a></li>
                        <li><a href="#about" class="text-gray-300 hover:text-blue-300 transition-colors duration-300">About Us</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-blue-300 transition-colors duration-300">Features</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-blue-300 transition-colors duration-300">Contact</a></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-white">Resources</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-blue-300 transition-colors duration-300">Documentation</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-blue-300 transition-colors duration-300">Tutorials</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-blue-300 transition-colors duration-300">Blog</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-blue-300 transition-colors duration-300">Support</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-white">Stay Updated</h3>
                    <p class="text-gray-300 mb-4">Subscribe to our newsletter for updates and tips.</p>
                    <form class="flex flex-col space-y-2">
                        <input type="email" placeholder="Enter your email"
                            class="px-4 py-2 bg-gray-600 text-white rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors duration-300">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="mt-12 pt-8 border-t border-gray-600 text-center">
                <p class="text-gray-300">© 2025 BuildiFy. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>