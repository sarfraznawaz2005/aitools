<x-doctalk::layouts.doctalk :title="'DocTalk'">

    <x-slot name="sidebar">
        <h2>Conversations</h2>
        <ul>
            <li><a href="#">Chat 1</a></li>
            <li><a href="#">Chat 2</a></li>
            <li><a href="#">Chat 3</a></li>
            <li><a href="#">Chat 4</a></li>
        </ul>
    </x-slot>

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

</x-doctalk::layouts.doctalk>
