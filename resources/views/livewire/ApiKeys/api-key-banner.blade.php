<div>
    @if(!$hasApiKeys)
        <x-banner type="info" title="Important" message="You don't have API key saved, please click on the button to create one." buttonText="Create API Key" buttonAttributes="data-hs-overlay=#general-modal" />
    @endif
</div>
