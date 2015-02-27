@extends('app')
@section('content')
<!-- Right side column. Contains the navbar and content of the page -->
<aside class="right-side">                
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header">
                        <i class='fa fa-file'></i>
                        <h3 class="box-title">foaf Agents</h3>
                    </div><!-- /.box-header -->

                    @include("provider_data._provider_selection_box")
                    
                    <div class="box-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Provider</th>
                                    <th>Name</th>                                    
                                </tr>   
                            </thead>
                            <tbody>
                                @foreach ($agents as $agent)
                                <tr>
                                    <td>{{ $agent->id }}</td>
                                    <td>{{ $agent->provider }}</td>
                                    <td><a href="{{ action('AgentController@show', $agent->id) }}">{{ $agent->name }}</a></td>                                                                        
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <?php echo $agents->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>              
</aside>
@endsection