<div class="bg-gray-50 {{request()->has('hideActions') ? '' : ' px-8 py-4'}}">

    @if (!request()->has('hideActions'))
        <div class="flex flex-col md:flex-row justify-center w-full gap-4 mb-4">
            <button wire:click="favorite"
                    class="bg-gray-50 hover:bg-gray-200 text-gray-600 border border-gray-200 font-medium py-2 px-4 rounded">
                ⭐ Favorite
            </button>
            <button wire:click="delete"
                    class="bg-gray-50 hover:bg-gray-200 text-gray-600 border border-gray-200 font-medium py-2 px-4 rounded">
                🗑️ Delete
            </button>
            <button id="shareButton" style="display: none;"
                    class="bg-gray-50 hover:bg-gray-200 text-gray-600 border border-gray-200 font-medium py-2 px-4 rounded">
                📤 Share
            </button>
            <button wire:click="close"
                    class="bg-gray-50 hover:bg-gray-200 text-gray-600 border border-gray-200 font-medium py-2 px-4 rounded">
                ❌ Close
            </button>
        </div>
    @endif

    <div
        class="rounded-lg p-3 my-4 border border-gray-300 w-fit justify-center uppercase m-auto bg-gray-200 font-[500] text-gray-600 text-base">
        {!! $tipContent->tip->name !!}
    </div>

    <div
        class="prose mx-auto px-6 {{request()->has('hideActions') ? 'bg-gray-50' : 'py-2 bg-white rounded-lg border border-gray-300 shadow-2xl'}}">
        <x-markdown id="contents">{!! $tipContent->content !!}</x-markdown>
    </div>

    <script data-navigate-once wire:ignore>
        document.addEventListener('DOMContentLoaded', (event) => {
            const shareButton = document.getElementById('shareButton');
            const markdownContent = document.getElementById('contents').innerText;

            if (navigator.share) {
                shareButton.style.display = 'block';
                shareButton.addEventListener('click', async () => {
                    try {
                        await navigator.share({
                            title: 'Check out this content',
                            text: markdownContent,
                            url: window.location.href
                        });
                        console.log('Content shared successfully');
                    } catch (error) {
                        console.error('Error sharing content:', error);
                    }
                });
            } else {
                shareButton.style.display = 'none';
            }
        });
    </script>
</div>
