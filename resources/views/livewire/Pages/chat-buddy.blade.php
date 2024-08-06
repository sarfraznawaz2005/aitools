<div>
    <livewire:chat.sidebar/>

    <div class="relative">
        <livewire:chat.chatlist :conversation="$conversation"/>

        <livewire:chat.chatinput :conversation="$conversation"/>
    </div>
</div>


