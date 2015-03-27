@extends('app')
@section('content')
<!-- Right side column. Contains the navbar and content of the page -->        
<aside class="right-side">                
    <!-- Main content -->
    <section class="content-header">
        <h1>
            Search
            <small>Here you can search all available {{ $type['name'] }} </small>
        </h1>
        <hr>
    </section>
    <!-- Main content -->
    <section class="content">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">

                    {!! Form::open(array("action" => "SearchController@search")) !!}
                    
                        <!-- search form -->
                        {!! Form::hidden("search_type", $type['id'], array("id" => "search_type")) !!}
                        
                        <div class="input-group">
                            {!! Form::text("q", null, array("id" => "q", "class" => "form-control", "placeholder" => "Search for ".$type["name"]."...")) !!}
                            <span class="input-group-btn">
                                {!! Form::submit("Search", array("class" => "btn btn-flat form-control", "style" => "border:1px #c0c0c0 solid;")) !!}
                            </span>
                        </div>
                        
                    {!! Form::close() !!}


                </div>
                <div class="col-md-3"></div>
            </div>
            <div class="row">
                <div class="col-md-12" id="search_results_box">
                    <div class='row'><div class='col-md-12'><hr/></div></div>
                    @foreach($drs as $dr)
                                               
                        <div class='col-md-6'>
                            <div class='box box-primary' id='dataresource_item' item_id='{{ $dr->id }}'>
                                <div class='box-body'>
                                    <div class='row'>
                                        <div class='col-md-2'>
                                            <img src='{{ asset("img/monument.png") }}' height='50' border='0'> 
                                        </div>
                                        <div class='col-md-10'>
                                            <!--<a href='#' id='dr_item_href' item_id='{{ $dr->id }}' data-toggle='modal' data-target='#item-modal'>{{ $dr->name }}</a>-->
                                            <a href="{{ action('DatabaseController@show', $dr->id) }}">{{ $dr->name }}</a>  
                                        </div>
                                    </div></br>
                                    <div class='row'>
                                        <div class='col-md-12'>
                                        <br/><br/>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class='col-md-10'>
                                            type: <b>{{ $type['name'] }}</b><br/>
                                            @foreach($dr->properties['ariadne_subject'] as $value)
                                            subject: <b>{{ $value }}</b> <br/>
                                            @endforeach
                                        </div>
                                        <div class='col-md-2 pull-right'>         
                                            <br/><br/><a href='#' id='dr_item_href' item_id='{{ $dr->id }}' data-toggle='modal' data-target='#item-modal'>more...</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>						
                 
                    @endforeach
                </div>
            </div>
            <div class="row">
                    <div class="col-md-12" id="search_results_more" p="1" style="display:none;">
                            <center><div class="btn btn-primary btn-sm"> more results </div></center>
                    </div>
            </div>
        
      
        
   </section>             
</aside>
@endsection