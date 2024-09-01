<div class="bg-gray-50 px-8 py-4">

    <h3 class="font-[500] text-gray-800 text-lg block w-full mb-2 border-b pb-2">
        {!! ucwords($note->title) !!}
    </h3>

    <div class="content prose mx-auto py-2">
        {!! $note->content !!}
    </div>

    <style>
        div.content {
            font-size: 1rem !important;
            line-height: 2rem !important;
        }

        iframe {
            width: 100% !important;
            height: 400px !important;
        }

        div.content img {
            width: 100% !important;
            max-height: 300px !important;
            height: auto !important;
            object-fit: contain !important;
        }
    </style>
</div>
