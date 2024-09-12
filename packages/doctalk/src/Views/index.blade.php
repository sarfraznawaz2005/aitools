<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocTalk</title>
    <link rel="stylesheet" href="/vendor/doctalk/assets/doctalk.css" />
    <script defer src="/vendor/doctalk/assets/doctalk.js"></script>
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

                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
                industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type
                and scrambled it to make a type specimen book.

                <div class="action-buttons">
                    <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Copy">
                        <x-doctalk::icons.copy/>
                    </div>

                    <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Delete">
                        <x-doctalk::icons.delete/>
                    </div>
                </div>
            </div>

            <div class="message received">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
                industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type
                and scrambled it to make a type specimen book.

                <div class="action-buttons">
                    <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Copy">
                        <x-doctalk::icons.copy/>
                    </div>

                    <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Delete">
                        <x-doctalk::icons.delete/>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sticky Chat Input -->
        <div class="chat-input">
            <form>
                <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Attach PDFs">
                    <button type="button" class="attach-button">
                        <x-doctalk::icons.attach/>
                    </button>
                </div>

                <input type="text" placeholder="Type your message...">

                <div class="tooltip-wrapper tooltip t-top" data-tooltip-text="Send Message">
                    <button type="submit" class="send-button">
                        <x-doctalk::icons.submit/>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
