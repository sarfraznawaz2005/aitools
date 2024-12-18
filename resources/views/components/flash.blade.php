@if (session('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 max-h-screen" x-transition:leave-end="opacity-0 max-h-0" class="flash-message" wire:key="flash-general-{{ uniqid() }}">
        <div class="mt-20 mb-4 bg-teal-50 border-t-2 border-teal-500 rounded-lg p-4 dark:bg-teal-800/30" role="alert"
             tabindex="-1"
             aria-labelledby="hs-bordered-success-style-label">
            <div class="flex">
                <div class="shrink-0">
                <span
                    class="inline-flex justify-center items-center size-8 rounded-full border-4 border-teal-100 bg-teal-200 text-teal-800 dark:border-teal-900 dark:bg-teal-800 dark:text-teal-400">
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
            <path d="m9 12 2 2 4-4"></path>
          </svg>
        </span>
                </div>

                <div class="ms-3">
                    <h3 id="hs-bordered-success-style-label" class="text-gray-800 font-semibold dark:text-white">
                        Success!
                    </h3>
                    <p class="text-sm text-gray-700 dark:text-neutral-400">
                        {{ session('message') }}
                        @php session()->forget('message') @endphp
                    </p>
                </div>

            </div>
        </div>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="mb-4 bg-red-50 border-s-4 border-red-500 p-4 dark:bg-red-800/30" role="alert" tabindex="-1"
         aria-labelledby="hs-bordered-red-style-label">
        <div class="flex">
            <div class="shrink-0">
                <span
                    class="inline-flex justify-center items-center size-8 rounded-full border-4 border-red-100 bg-red-200 text-red-800 dark:border-red-900 dark:bg-red-800 dark:text-red-400">
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 6 18"></path>
            <path d="m6 6 12 12"></path>
          </svg>
        </span>
            </div>

            <div class="ms-3">
                <h3 id="hs-bordered-red-style-label" class="text-gray-600 font-semibold dark:text-white">
                    Oops!
                </h3>

                <ul class="mt-3 list-disc list-inside text-sm text-red-600 dark:text-red-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
@endif
