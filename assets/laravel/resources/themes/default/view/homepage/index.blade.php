@extends('theme::layouts.main')

@section('content')

    {!! $content ?? '' !!}




    <div class = "container">
        <div class = "row">
            <div class = "col-xs-12">
                <div class = "page-header">
                    <h1>Simplo.cz
                        <small>Default template</small>
                    </h1>
                </div>
            </div>
            <div class = "col-xs-12">
                <div class = "jumbotron">
                    <h1>The best place for new beginning</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <i style="color:#df0078;margin-top:20px;" class="far fa-child" data-fa-transform="grow-100"></i>
    </div>





@endsection