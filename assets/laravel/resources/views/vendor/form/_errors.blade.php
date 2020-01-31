@if (count($errors) > 0)
    <div class="alert alert-danger alert-dismissable">
        @foreach($errors->all() as $error)
            <i class="fa fa-warning"></i>
            {{ $error }}
            <br>
        @endforeach
    </div>
@endif

@if (isset($status))
    <div class="alert alert-success">
        {{ $status }}
    </div>
@endif
