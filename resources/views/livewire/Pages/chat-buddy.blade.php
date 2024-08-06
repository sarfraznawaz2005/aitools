<style>
    .group:hover .group-hover\:inline-block {
        display: inline-block;
    }
</style>

<div>
    @livewire('chat.sidebar')

    <div class="relative h-screen w-full lg:ps-64">
        @livewire('chat.chatlist')
        @livewire('chat.chatinput')
    </div>
</div>


