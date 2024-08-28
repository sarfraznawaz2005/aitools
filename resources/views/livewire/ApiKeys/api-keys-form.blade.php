<div>

    @if ($this->apiKeys && count($this->apiKeys))
        <fieldset class="border border-gray-300 rounded-lg p-4 dark:border-neutral-700 mb-4">
            <legend class="text-sm font-medium text-gray-500 dark:text-neutral-300">SAVED API KEYS</legend>

            <div class="items-center justify-center font-medium w-full border border-gray-300">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gray-50 dark:bg-neutral-800">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                            Model Name
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                            Type
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-700 dark:divide-neutral-600">
                    @foreach($this->apiKeys as $apiKey)
                        <tr wire:key="apikeyrow-{{ $apiKey->id }}">
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300">
                                {{ $apiKey->model_name }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300">
                                {{ $apiKey->llm_type }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 text-center">
                                @if ($apiKey->active)
                                    <button x-data x-tooltip.raw="This is currently default"
                                            class="cursor-default items-center px-2 py-1 text-white bg-green-600 rounded mr-2">
                                        <x-icons.ok class="w-4 h-4 mx-auto"/>
                                    </button>
                                @else
                                    <button x-data x-tooltip.raw="Make Default"
                                            wire:click="markDefault({{ $apiKey->id }})"
                                            class="items-center px-2 py-1 text-white bg-gray-600 hover:bg-gray-800 rounded mr-2">
                                        <x-icons.ok class="w-4 h-4 mx-auto"/>
                                    </button>
                                @endif

                                <button
                                    x-data x-tooltip.raw="Edit"
                                    wire:click="edit({{ $apiKey->id }})"
                                    class="items-center px-2 py-1 text-white bg-blue-600 hover:bg-blue-800 rounded mr-2">
                                    <x-icons.edit class="w-4 h-4 mx-auto"/>
                                </button>

                                <x-confirm-dialog call="deleteApiKey({{$apiKey->id}})" x-data x-tooltip.raw="Delete"
                                                  class="px-2 py-1 text-white bg-red-600 hover:bg-red-800 rounded">
                                    <x-icons.delete class="w-4 h-4 mx-auto"/>
                                </x-confirm-dialog>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </fieldset>
    @endif

    <x-flash/>

    <fieldset class="border border-gray-300 rounded-lg p-4 dark:border-neutral-700">
        <legend class="text-sm font-medium text-gray-500 dark:text-neutral-300">
            {{ $model->exists ? 'EDIT API KEY' : 'ADD API KEY'}}
        </legend>

        @if ($model->exists)
            <button wire:click="resetForm"
                    class="flex justify-center items-center py-1 px-4 mb-8 text-gray-600 bg-red-100 rounded hover:bg-red-200">
                <x-icons.close class="size-5 mr-2 inline-block"/>
                Cancel
            </button>
        @endif

        <!-- Select -->
        <div class="relative mb-3" x-data x-init="
              $nextTick(() => {
                HSSelect.autoInit();
              });

              $wire.on('updated', () => {
                $nextTick(() => {
                  HSSelect.autoInit();
                });
              });
            ">

            <!-- Select -->
            <div>
                <select wire:model="llm_type" id="llm_type" x-data x-init="
                  $nextTick(() => {
                    window.HSStaticMethods.autoInit();

                    $watch('$el', () => {
                      window.HSStaticMethods.autoInit();
                    });
                  })
                "
                        data-hs-select='{
                  "placeholder": "Choose LLM",
                  "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 \" data-title></span></button>",
                  "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-none focus:ring-2 focus:ring-blue-500",
                  "dropdownClasses": "mt-2 max-h-72 p-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300",
                  "optionClasses": "py-2 px-3 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100",
                  "optionTemplate": "<div id=\"foobar\" class=\"flex items-center\"><div class=\"me-2\" data-icon></div><div><div class=\"hs-selected:font-semibold text-sm text-gray-800 \" data-title></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" viewBox=\"0 0 16 16\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
                  "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                }' class="hidden">
                    <option value="">Choose LLM</option>
                    <option selected="" value="OpenAI" data-hs-select-option='{
                        "icon": " <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"shrink-0 size-5\" preserveAspectRatio=\"xMidYMid\" viewBox=\"0 0 256 260\"> <path d=\"M239.184 106.203a64.716 64.716 0 0 0-5.576-53.103C219.452 28.459 191 15.784 163.213 21.74A65.586 65.586 0 0 0 52.096 45.22a64.716 64.716 0 0 0-43.23 31.36c-14.31 24.602-11.061 55.634 8.033 76.74a64.665 64.665 0 0 0 5.525 53.102c14.174 24.65 42.644 37.324 70.446 31.36a64.72 64.72 0 0 0 48.754 21.744c28.481.025 53.714-18.361 62.414-45.481a64.767 64.767 0 0 0 43.229-31.36c14.137-24.558 10.875-55.423-8.083-76.483Zm-97.56 136.338a48.397 48.397 0 0 1-31.105-11.255l1.535-.87 51.67-29.825a8.595 8.595 0 0 0 4.247-7.367v-72.85l21.845 12.636c.218.111.37.32.409.563v60.367c-.056 26.818-21.783 48.545-48.601 48.601Zm-104.466-44.61a48.345 48.345 0 0 1-5.781-32.589l1.534.921 51.722 29.826a8.339 8.339 0 0 0 8.441 0l63.181-36.425v25.221a.87.87 0 0 1-.358.665l-52.335 30.184c-23.257 13.398-52.97 5.431-66.404-17.803ZM23.549 85.38a48.499 48.499 0 0 1 25.58-21.333v61.39a8.288 8.288 0 0 0 4.195 7.316l62.874 36.272-21.845 12.636a.819.819 0 0 1-.767 0L41.353 151.53c-23.211-13.454-31.171-43.144-17.804-66.405v.256Zm179.466 41.695-63.08-36.63L161.73 77.86a.819.819 0 0 1 .768 0l52.233 30.184a48.6 48.6 0 0 1-7.316 87.635v-61.391a8.544 8.544 0 0 0-4.4-7.213Zm21.742-32.69-1.535-.922-51.619-30.081a8.39 8.39 0 0 0-8.492 0L99.98 99.808V74.587a.716.716 0 0 1 .307-.665l52.233-30.133a48.652 48.652 0 0 1 72.236 50.391v.205ZM88.061 139.097l-21.845-12.585a.87.87 0 0 1-.41-.614V65.685a48.652 48.652 0 0 1 79.757-37.346l-1.535.87-51.67 29.825a8.595 8.595 0 0 0-4.246 7.367l-.051 72.697Zm11.868-25.58 28.138-16.217 28.188 16.218v32.434l-28.086 16.218-28.188-16.218-.052-32.434Z\"/> </svg> "}'>
                        OpenAI
                    </option>
                    <option value="Gemini" data-hs-select-option='{
                        "icon": " <svg class=\"shrink-0 size-5\" viewBox=\"0 0 256 262\" xmlns=\"http://www.w3.org/2000/svg\" preserveAspectRatio=\"xMidYMid\"> <path d=\"M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027\" fill=\"#4285F4\"/><path d=\"M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1\" fill=\"#34A853\"/><path d=\"M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782\" fill=\"#FBBC05\"/><path d=\"M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251\" fill=\"#EB4335\"/> </svg> "}'>
                        Gemini
                    </option>
                    <option value="Ollama" data-hs-select-option='{
                        "icon": " <svg class=\"shrink-0 size-5\" viewBox=\"0 0 646 854\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"> <path d=\"M140.629 0.239929C132.66 1.52725 123.097 5.69568 116.354 10.845C95.941 26.3541 80.1253 59.2728 73.4435 100.283C70.9302 115.792 69.2138 137.309 69.2138 153.738C69.2138 173.109 71.4819 197.874 74.7309 214.977C75.4665 218.778 75.8343 222.15 75.5278 222.395C75.2826 222.64 72.2788 225.092 68.9072 227.789C57.3827 236.984 44.2029 251.145 35.1304 264.08C17.7209 288.784 6.44151 316.86 1.72133 347.265C-0.117698 359.28 -0.608106 383.555 0.863118 395.57C4.11207 423.278 12.449 446.695 26.7321 468.151L31.391 475.078L30.0424 477.346C20.4794 493.407 12.3264 516.64 8.52575 538.953C5.522 556.608 5.15419 561.328 5.15419 584.99C5.15419 608.837 5.4607 613.557 8.28054 630.047C11.6521 649.786 18.5178 670.689 26.1804 684.605C28.6938 689.141 34.8239 698.581 35.5595 699.072C35.8047 699.194 35.0691 701.462 33.9044 704.098C25.077 723.408 17.537 749.093 14.4106 770.733C12.2038 785.567 11.8973 790.349 11.8973 805.981C11.8973 825.903 13.0007 835.589 17.1692 851.466L17.7822 853.795H44.019H70.3172L68.6007 850.546C57.9957 830.93 57.0149 794.517 66.1487 758.166C70.3172 741.369 75.0374 729.048 83.8647 712.067L89.1366 701.769V695.455C89.1366 689.57 89.014 688.896 87.1137 685.034C85.6424 682.091 83.6808 679.578 80.1866 676.145C74.2404 670.383 69.9494 664.314 66.5165 656.835C51.4365 624.1 48.494 575.489 59.0991 534.049C63.5128 516.762 70.8076 501.376 78.4702 492.978C83.6808 487.215 86.378 480.779 86.378 474.097C86.378 467.17 83.926 461.469 78.4089 455.523C62.5932 438.604 52.8464 418.006 49.3522 394.038C44.3868 359.893 53.3981 322.683 73.8726 293.198C93.9181 264.263 122.055 245.689 153.503 240.724C160.552 239.559 173.732 239.743 181.088 241.092C189.119 242.502 194.145 242.072 199.295 239.62C205.67 236.617 208.858 232.877 212.597 224.295C215.907 216.633 218.482 212.464 225.409 203.821C233.746 193.461 241.776 186.411 254.649 177.89C269.362 168.266 286.097 161.278 302.771 157.906C308.839 156.68 311.659 156.496 323 156.496C334.341 156.496 337.161 156.68 343.229 157.906C367.688 162.872 391.964 175.5 411.335 193.399C415.503 197.261 425.495 209.644 428.683 214.794C429.909 216.816 432.055 221.108 433.403 224.295C437.142 232.877 440.33 236.617 446.705 239.62C451.671 242.011 456.881 242.502 464.605 241.214C476.804 239.13 486.183 239.314 498.137 241.766C538.841 249.98 574.273 283.512 589.966 328.446C603.636 367.862 599.774 409.118 579.422 440.626C575.989 445.96 572.556 450.251 567.591 455.523C556.863 466.986 556.863 481.208 567.53 492.978C585.062 512.165 596.035 559.367 592.724 600.99C590.518 628.453 583.468 653.035 573.782 666.95C572.066 669.402 568.511 673.57 565.813 676.145C562.319 679.578 560.358 682.091 558.886 685.034C556.986 688.896 556.863 689.57 556.863 695.455V701.769L562.135 712.067C570.963 729.048 575.683 741.369 579.851 758.166C588.863 794.027 588.066 829.704 577.767 849.995C576.909 851.711 576.173 853.305 576.173 853.489C576.173 853.673 587.882 853.795 602.226 853.795H628.218L628.892 851.159C629.26 849.75 629.873 847.604 630.179 846.378C630.854 843.681 632.202 835.712 633.306 828.049C634.348 820.325 634.348 791.881 633.306 783.299C629.383 752.158 622.823 727.454 612.096 704.098C610.931 701.462 610.195 699.194 610.44 699.072C610.747 698.888 612.463 696.436 614.302 693.677C627.666 673.448 635.88 648.008 640.049 614.415C641.152 605.158 641.152 565.374 640.049 556.485C637.106 533.559 633.551 517.988 627.666 502.234C625.214 495.675 618.716 481.821 615.958 477.346L614.609 475.078L619.268 468.151C633.551 446.695 641.888 423.278 645.137 395.57C646.608 383.555 646.118 359.28 644.279 347.265C639.497 316.798 628.279 288.845 610.87 264.08C601.797 251.145 588.617 236.984 577.093 227.789C573.721 225.092 570.717 222.64 570.472 222.395C570.166 222.15 570.534 218.778 571.269 214.977C578.687 176.296 578.441 128.053 570.656 90.3524C563.913 57.4951 551.653 31.3808 535.837 16.3008C523.209 4.28578 510.336 -0.863507 494.888 0.11731C459.456 2.20154 430.89 42.9667 419.61 107.21C417.771 117.57 416.178 129.708 416.178 133.018C416.178 134.305 415.932 135.347 415.626 135.347C415.319 135.347 412.929 134.121 410.354 132.589C383.014 116.405 352.608 107.762 323 107.762C293.392 107.762 262.986 116.405 235.646 132.589C233.071 134.121 230.681 135.347 230.374 135.347C230.068 135.347 229.822 134.305 229.822 133.018C229.822 129.585 228.167 117.08 226.39 107.21C216.152 49.5259 192.674 11.3354 161.472 1.71112C157.181 0.423799 144.982 -0.434382 140.629 0.239929ZM151.051 50.139C159.878 57.1273 169.686 77.1114 175.326 99.4863C176.368 103.532 177.471 108.191 177.778 109.907C178.023 111.563 178.697 115.302 179.249 118.183C181.64 131.179 182.743 145.217 182.866 162.32L182.927 179.178L178.697 185.43L174.468 191.744H164.598C153.074 191.744 141.61 193.216 130.637 196.158C126.714 197.139 122.913 198.12 122.178 198.304C121.013 198.549 120.829 198.181 120.155 193.154C116.538 165.875 116.722 135.654 120.707 110.52C125.12 82.5059 135.419 57.1273 145.472 49.6486C147.863 47.8708 148.292 47.9321 151.051 50.139ZM500.589 49.7098C506.658 54.1848 513.34 66.0772 518.305 81.2798C528.297 111.685 531.117 153.431 525.845 193.154C525.171 198.181 524.987 198.549 523.822 198.304C523.087 198.12 519.286 197.139 515.363 196.158C504.39 193.216 492.926 191.744 481.402 191.744H471.532L467.303 185.43L463.073 179.178L463.134 162.32C463.257 138.535 465.464 119.961 470.735 99.3024C476.314 77.1114 486.183 57.1273 494.949 50.139C497.708 47.9321 498.137 47.8708 500.589 49.7098Z\" fill=\"black\"/> <path d=\"M313.498 358.237C300.195 359.525 296.579 360.015 290.203 361.303C279.843 363.448 265.989 368.23 256.365 372.95C222.895 389.317 199.846 416.596 192.796 448.166C191.386 454.419 191.202 456.503 191.202 467.047C191.202 477.468 191.386 479.736 192.735 485.682C202.114 526.938 240.12 557.405 289.284 562.983C299.95 564.148 346.049 564.148 356.715 562.983C396.193 558.508 430.154 537.114 445.418 507.076C449.463 499.046 451.425 493.835 453.264 485.682C454.613 479.736 454.797 477.468 454.797 467.047C454.797 456.503 454.613 454.419 453.203 448.166C442.965 402.313 398.461 366.207 343.903 359.341C336.792 358.483 318.157 357.747 313.498 358.237ZM336.424 391.585C354.631 393.547 372.96 400.045 387.672 409.853C395.58 415.125 406.737 426.159 411.518 433.393C417.403 442.342 420.774 451.476 422.307 462.572C422.981 467.66 422.614 471.522 420.774 479.736C417.893 491.996 408.943 504.808 396.867 513.758C391.227 517.865 379.519 523.812 372.347 526.141C358.738 530.493 349.849 531.29 318.095 531.045C297.376 530.861 293.697 530.677 287.751 529.574C267.461 525.773 251.4 517.681 239.753 505.36C230.312 495.429 226.021 486.357 223.692 471.706C222.65 464.901 224.611 453.622 228.596 444.12C233.439 432.534 245.944 418.129 258.327 409.853C272.671 400.29 291.552 393.486 308.9 391.647C315.582 390.911 329.742 390.911 336.424 391.585Z\" fill=\"black\"/> <path d=\"M299.584 436.336C294.925 438.849 291.676 445.224 292.657 449.944C293.76 455.032 298.235 460.182 305.223 464.412C308.963 466.68 309.208 466.986 309.392 469.254C309.514 470.603 309.024 474.465 308.35 477.898C307.614 481.269 307.062 484.825 307.062 485.806C307.124 488.442 309.576 492.733 312.15 494.817C314.419 496.656 314.848 496.717 321.223 496.901C327.047 497.085 328.273 496.962 330.602 495.859C336.61 492.916 338.142 487.522 335.935 477.162C334.096 468.519 334.464 467.17 339.062 464.534C343.904 461.714 349.054 456.749 350.586 453.377C353.529 446.941 350.831 439.646 344.333 436.274C342.74 435.477 340.778 435.11 337.897 435.11C333.422 435.11 330.541 436.152 325.269 439.523L322.265 441.424L320.365 440.259C312.58 435.661 311.17 435.11 306.449 435.171C303.078 435.171 301.239 435.477 299.584 436.336Z\" fill=\"black\"/> <path d=\"M150.744 365.165C139.894 368.598 131.802 376.567 127.634 387.908C125.611 393.303 124.63 401.824 125.488 406.421C127.511 417.394 136.522 427.386 146.76 430.145C159.633 433.516 169.257 431.309 177.778 422.85C182.743 418.007 185.441 413.777 188.138 406.911C190.099 402.069 190.222 401.211 190.222 394.345L190.283 386.989L187.709 381.717C183.601 373.38 176.184 367.188 167.602 364.92C162.759 363.694 154.974 363.756 150.744 365.165Z\" fill=\"black\"/> <path d=\"M478.153 364.982C469.755 367.25 462.276 373.502 458.291 381.717L455.717 386.989L455.778 394.345C455.778 401.211 455.901 402.069 457.862 406.911C460.56 413.777 463.257 418.007 468.222 422.85C476.743 431.309 486.367 433.516 499.241 430.145C506.658 428.183 514.075 421.93 517.631 414.635C520.696 408.444 521.431 403.969 520.451 396.919C518.183 380.797 508.742 369.089 494.704 364.982C490.597 363.756 482.628 363.756 478.153 364.982Z\" fill=\"black\"/> </svg> "}'>
                        Ollama
                    </option>
                </select>
                <!-- End Select -->
            </div>
        </div>

        <!-- End Select -->

        <!-- Floating Input -->
        <div class="relative mb-3" x-show="$wire.llm_type === 'Ollama'">
            <input type="url" wire:model="base_url"
                   placeholder="Base URL"
                   class="peer p-3 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
        </div>
        <!-- End Floating Input -->

        <!-- Floating Input -->
        <div class="relative mb-3">
            <input type="text" wire:model="api_key"
                   placeholder="API Key"
                   class="peer p-3 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
        </div>
        <!-- End Floating Input -->

        <!-- Floating Input -->
        <div class="relative">
            <input type="text" wire:model="model_name"
                   placeholder="Model Name"
                   class="peer p-3 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">

            <div class="mt-2 text-xs text-gray-400 dark:text-neutral-500">
                <button class="text-blue-500 text-xs ml-2" @click.prevent="$dispatch('open-dialog')">
                    See Current Models
                </button>

                <!-- Dialog Component -->
                <x-dialog>
                    <ul class="space-y-3">
                        <li>
                            <h3 class="font-semibold text-base mb-2">OpenAI</h3>
                            <ul class="ml-4 space-y-2">
                                <li class="flex items-center">
                                    <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full mr-2"></span>
                                    <span class="text-blue-500 text-sm cursor-pointer"
                                          @click="
                                            open = false;
                                            $refs.dialog.close();
                                            $nextTick(() => {
                                                $wire.set('model_name', $el.innerText.trim());
                                            });
                                          ">
                                        gpt-4o-mini
                                    </span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full mr-2"></span>
                                    <span class="text-blue-500 text-sm cursor-pointer"
                                          @click="
                                            open = false;
                                            $refs.dialog.close();
                                            $nextTick(() => {
                                                $wire.set('model_name', $el.innerText.trim());
                                            });
                                          ">
                                        gpt-4o
                                    </span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full mr-2"></span>
                                    <span class="text-blue-500 text-sm cursor-pointer"
                                          @click="
                                            open = false;
                                            $refs.dialog.close();
                                            $nextTick(() => {
                                                $wire.set('model_name', $el.innerText.trim());
                                            });
                                          ">
                                        gpt-4-turbo
                                    </span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full mr-2"></span>
                                    <span class="text-blue-500 text-sm cursor-pointer"
                                          @click="
                                            open = false;
                                            $refs.dialog.close();
                                            $nextTick(() => {
                                                $wire.set('model_name', $el.innerText.trim());
                                            });
                                          ">
                                        gpt-3.5-turbo
                                    </span>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <h3 class="font-semibold text-base mb-2">Google</h3>
                            <ul class="ml-4 space-y-2">
                                <li class="flex items-center">
                                    <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full mr-2"></span>
                                    <span class="text-blue-500 text-sm cursor-pointer"
                                          @click="
                                            open = false;
                                            $refs.dialog.close();
                                            $nextTick(() => {
                                                $wire.set('model_name', $el.innerText.trim());
                                            });
                                          ">
                                        gemini-1.5-flash-latest
                                    </span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full mr-2"></span>
                                    <span class="text-blue-500 text-sm cursor-pointer"
                                          @click="
                                            open = false;
                                            $refs.dialog.close();
                                            $nextTick(() => {
                                                $wire.set('model_name', $el.innerText.trim());
                                            });
                                          ">
                                        gemini-1.5-flash
                                    </span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full mr-2"></span>
                                    <span class="text-blue-500 text-sm cursor-pointer"
                                          @click="
                                            open = false;
                                            $refs.dialog.close();
                                            $nextTick(() => {
                                                $wire.set('model_name', $el.innerText.trim());
                                            });
                                          ">
                                        gemini-1.5-pro
                                    </span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full mr-2"></span>
                                    <span class="text-blue-500 text-sm cursor-pointer"
                                          @click="
                                            open = false;
                                            $refs.dialog.close();
                                            $nextTick(() => {
                                                $wire.set('model_name', $el.innerText.trim());
                                            });
                                          ">
                                        gemini-pro
                                    </span>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <h3 class="font-semibold text-base mb-2">Ollama</h3>
                            <ul class="ml-4 space-y-2">
                                <li class="flex items-center">
                                    <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full mr-2"></span>
                                    <span class="text-sm">Models you have downloaded on your PC.</span>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </x-dialog>
            </div>

            <p class="mt-2 ml-2 text-xs text-gray-400 dark:text-neutral-500">
                Or for latest model, see
                <a class="text-blue-500" href="https://ai.google.dev/gemini-api/docs/models/gemini" target="_blank">Google</a>
                |
                <a class="text-blue-500" href="https://platform.openai.com/docs/models" target="_blank">OpenAI</a> |
                <a class="text-blue-500" href="https://ollama.com/library" target="_blank">Ollama</a>
            </p>
        </div>
        <!-- End Floating Input -->

        <div
            class="flex justify-end items-center gap-x-4 mt-4">
            <x-gradient-button wire:click="save">
                <x-icons.ok class="size-5"/>
                Save
            </x-gradient-button>
        </div>
    </fieldset>


</div>
