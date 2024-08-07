<div>
    <livewire:chat.sidebar/>

    <div class="relative h-screen w-full lg:ps-64">
        <livewire:chat.chatlist :conversation="$conversation"/>

        <livewire:chat.chatinput :conversation="$conversation"/>
    </div>
</div>
