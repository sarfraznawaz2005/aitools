<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Interface</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media (max-width: 640px) {
            .sidebar-open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="h-screen m-0 p-0">

<div class="flex h-screen overflow-hidden font-sans text-gray-800">
    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 sm:w-80 bg-gray-100 overflow-y-auto overflow-x-hidden border-r border-gray-200 transition-transform duration-300 ease-in-out transform -translate-x-full">
        <h2 class="text-xl font-semibold text-gray-600 my-5 text-center">Conversations</h2>
        <ul>
            <li class="mb-2"><a href="#" class="block px-3 py-3 hover:bg-gray-200 transition-colors duration-100">Chat 1</a></li>
            <li class="mb-2"><a href="#" class="block px-3 py-3 hover:bg-gray-200 transition-colors duration-100">Chat 2</a></li>
            <li class="mb-2"><a href="#" class="block px-3 py-3 hover:bg-gray-200 transition-colors duration-100">Chat 3</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="flex-1 flex flex-col w-full transition-all duration-300 ease-in-out">
        <!-- Top bar -->
        <div class="bg-white border-b border-gray-200 p-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold">Chat Interface</h1>
            <button onclick="toggleSidebar()" class="p-2 rounded-md hover:bg-gray-200" title="Toggle Sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Chat Messages -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6">
            <div class="space-y-4">
                <div class="max-w-full sm:max-w-[70%] p-3 rounded-lg bg-green-100 ml-auto">
                    <p class="text-base leading-relaxed">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                    <div class="flex justify-end items-center gap-3 mt-3">
                        <button class="p-1 rounded hover:bg-green-200" title="Copy">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"/>
                            </svg>
                        </button>
                        <button class="p-1 rounded hover:bg-green-200" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="max-w-full sm:max-w-[70%] p-3 rounded-lg bg-gray-100">
                    <p class="text-base leading-relaxed">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                    <div class="flex justify-end items-center gap-3 mt-3">
                        <button class="p-1 rounded hover:bg-gray-200" title="Copy">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"/>
                            </svg>
                        </button>
                        <button class="p-1 rounded hover:bg-gray-200" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="p-4 bg-white border-t border-gray-200">
            <form class="flex items-center w-full bg-gray-100 rounded-full px-4 py-2">
                <button type="button" class="mr-3" title="Attach PDFs">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/>
                    </svg>
                </button>
                <input type="text" placeholder="Type your message..." class="flex-1 bg-transparent outline-none px-2 py-3">
                <button type="submit" class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-blue-700 transition-colors duration-300" title="Send Message">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M2 21L23 12 2 3v7l15 2-15 2v7z"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');

    function toggleSidebar() {
        sidebar.classList.toggle('-translate-x-full');
        mainContent.classList.toggle('sm:ml-80');
    }

    function updateSidebarState() {
        if (window.innerWidth >= 640) {
            sidebar.classList.remove('-translate-x-full');
            mainContent.classList.add('sm:ml-80');
        } else {
            sidebar.classList.add('-translate-x-full');
            mainContent.classList.remove('sm:ml-80');
        }
    }

    // Initialize sidebar state on page load
    window.addEventListener('load', updateSidebarState);

    // Update sidebar state on window resize
    window.addEventListener('resize', updateSidebarState);
</script>
</body>
</html>
