<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Interface</title>
    <style>

        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* Reset styles */
        .chat-container * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            color: #1a1a1a;
        }


        /* chat-container */
        .chat-container {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 300px;
            background-color: #f7f8f9;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 20px auto;
            border-right: 1px solid #e0e0e0;
            position: relative;
            height: 100vh;
            transform: translateX(0);
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
        }

        .sidebar.hidden {
            width: 0;
            padding: 0;
            border-right: none;
            transform: translateX(-100%);
        }

        .sidebar h2 {
            margin: 20px 0;
            display:flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 600;
            color: #555;
        }

        .sidebar ul {
            list-style-type: none;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            display: block;
            text-decoration: none;
            color: #333;
            padding: 12px;
            font-size: 16px;
        }

        .sidebar ul li a:hover {
            background-color: #e2e6e9;
            transition: background-color 0.1s ease-in-out;
        }

        /* Sidebar toggle button */
        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            position: absolute;
            top: 5px;
            left: 10px;
            z-index: 1001;
            color: #777;
            transition: color 0.3s;
        }

        .sidebar-toggle:hover {
            color: #444;
        }

        /* Main content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease-in-out;
            width: calc(100% - 300px);
        }

        .main-content.full-width {
            width: 100%;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 40px;
        }

        .chat-input {
            padding: 15px;
            background-color: #fff;
            display: flex;
            align-items: center;
            margin: 0 20px 5px 20px;
        }

        .chat-input form {
            display: flex;
            align-items: center;
            width: 100%;
            position: relative;
            background-color: #f0f2f5;
            border-radius: 360px;
            padding: 5px 15px;
        }

        .chat-input .attach-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            margin-right: 10px;
        }

        .chat-input .attach-button svg {
            width: 24px;
            height: 24px;
            transition: fill 0.3s;
        }

        .chat-input input[type="text"] {
            flex: 1;
            padding: 15px 5px;
            font-size: 16px;
            border: none;
            outline: none;
            background-color: transparent;
        }

        .chat-input .send-button {
            background-color: #1a73e8;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .chat-input .send-button:hover {
            background-color: #174ea6;
        }

        .chat-input .send-button svg {
            width: 20px;
            height: 20px;
            fill: #fff;
        }

        /* Messages */
        .message {
            max-width: 70%;
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            clear: both;
            font-size: 16px;
            line-height: 1.4;
        }

        .message.received {
            background-color: #f0f2f5;
            float: left;
            border-bottom-left-radius: 4px;
        }

        .message.sent {
            background-color: #e5fcd4;
            float: right;
            border-bottom-right-radius: 4px;
        }

        .message .action-buttons {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            margin-top: 12px;
            transition: opacity 0.3s;
        }

        .message .action-buttons svg {
            width: 18px;
            height: 18px;
            cursor: pointer;
            transition: fill 0.3s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 300px;
                position: fixed;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                height: 100vh;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar-toggle {
                z-index: 1001;
            }

            .main-content {
                order: 1;
                width: 100%;
            }

            .message {
                max-width: 85%;
            }
        }

        .tooltip-wrapper {
            width: fit-content;
            display: flex;
            align-items: center;
            flex-direction: row;
        }

        .tooltip{
            position: relative;
        }

        .tooltip::before {
            position: absolute;
            content: ' ';
            background-color: #333;
            width: 15px;
            height: 15px;
            z-index: 500;
            opacity: 0;
            transition: all cubic-bezier(0.17, 0.67, 0.5, 0.71) 100ms;
            pointer-events: none;
        }

        .tooltip::after {
            content: attr(data-tooltip-text);
            white-space: nowrap;
            background-color: #333;
            border-radius: 5px;
            color: #fff;
            position: absolute;
            text-align: center;
            font-size:14px;
            z-index: 555;
            opacity: 0;
            transition: all cubic-bezier(0.17, 0.67, 0.5, 0.71) 100ms;
            pointer-events: none;
            height: 30px;
            display: flex;
            align-items: center;
            padding: 0 10px 3px 10px;
        }

        .tooltip:hover::before, .tooltip:hover::after {
            opacity: 1;
        }

        .tooltip.t-top::before {
            top: 0;
            left: 50%;
            transform: translate(-50%, 0) rotate(45deg);
        }

        .tooltip.t-top::after {
            top: 0;
            left: 50%;
            transform: translate(-50%, 0);
        }

        .tooltip.t-top:hover::before {
            transform: translate(-50%, calc(-100% - 5px)) rotate(45deg);
        }

        .tooltip.t-top:hover::after {
            transform: translate(-50%, calc(-100% - 10px));
        }

        .tooltip.t-left::before {
            top: 25%;
            left: 0;
            transform: translate(0, 0%) rotate(45deg);
        }

        .tooltip.t-left::after {
            top: 0;
            left: 0;
        }

        .tooltip.t-left:hover::before {
            transform: translate(calc(-100% - 5px)) rotate(45deg);
        }

        .tooltip.t-left:hover::after {
            transform: translate(calc(-100% - 10px));
        }

        .tooltip.t-right::before {
            top: 25%;
            right: 0;
            transform: translate(0, 0%) rotate(45deg);
        }

        .tooltip.t-right::after {
            top: 0;
            right: 0;
        }

        .tooltip.t-right:hover::before {
            transform: translate(calc(100% + 5px)) rotate(45deg);
        }

        .tooltip.t-right:hover::after {
            transform: translate(calc(100% + 10px));
        }

        .tooltip.t-bottom::before {
            bottom: 0;
            left: 50%;
            transform: translate(-50%, 0) rotate(45deg);
        }

        .tooltip.t-bottom::after {
            bottom: 0;
            left: 50%;
            transform: translate(-50%, 0);
        }

        .tooltip.t-bottom:hover::before {
            transform: translate(-50%, calc(100% + 5px)) rotate(45deg);
        }

        .tooltip.t-bottom:hover::after {
            transform: translate(-50%, calc(100% + 10px));
        }
    </style>
</head>
<body>
<button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
<div class="chat-container">
    <!-- Sticky Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Conversations</h2>
        <ul>
            <li><a href="#">Chat 1</a></li>
            <li><a href="#">Chat 2</a></li>
            <li><a href="#">Chat 3</a></li>
        </ul>
    </div>

    <!-- Content Area -->
    <div class="main-content" id="main-content">
        <!-- Chat Messages -->
        <div class="chat-messages">
            <div class="message sent">
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                <div class="action-buttons">
                    <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Copy">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                        </svg>
                    </div>

                    <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Delete">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="message received">
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                <div class="action-buttons">
                    <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Copy">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                        </svg>
                    </div>

                    <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Delete">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </div>
                </div>
            </div>


        </div>

        <!-- Sticky Chat Input -->
        <div class="chat-input">
            <form>
                <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Attach PDFs">
                    <button type="button" class="attach-button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                        </svg>
                    </button>
                </div>

                <input type="text" placeholder="Type your message...">

                <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Send Message">
                    <button type="submit" class="send-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M2 21L23 12 2 3v7l15 2-15 2v7z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');

        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('active');
        } else {
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('full-width');
        }
    }

    // Check screen size on load and resize
    function checkScreenSize() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');

        if (window.innerWidth <= 768) {
            sidebar.classList.remove('hidden');
            sidebar.classList.remove('active');
            mainContent.classList.add('full-width');
        } else {
            sidebar.classList.remove('active');
            sidebar.classList.remove('hidden');
            mainContent.classList.remove('full-width');
        }
    }

    window.addEventListener('load', checkScreenSize);
    window.addEventListener('resize', checkScreenSize);
</script>
</body>
</html>
