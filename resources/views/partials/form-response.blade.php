@if ($success)
    <div class="alert alert-success">
        {{ $message }}
    </div>
@else
    <div class="alert alert-danger">
        {{ $message }}
    </div>
@endif