@extends('app')

@section('content')
<!-- Right side column. Contains the navbar and content of the page -->
<aside class="right-side">                

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-5">
                <div class="box box-warning">
                    <div class="box-header">
                        <i class='fa fa-home'></i>
                        <h3 class="box-title">Welcome</h3>
                    </div>
                    <div class="box-body">
                        <p>
                            The portal
                        </p>
                        <div class="quote">{{ Inspiring::quote() }}</div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
            <div class="col-md-7">
                <div class="box box-danger">
                    <div class="box-header">
                        <i class='fa fa-user'></i>
                        <h3 class="box-title">Providers</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Collections</th>
                                            <th>Datasets</th>
                                            <th>Databases</th>
                                        </tr>   
                                    </thead>
                                    <tbody>
                                        @foreach ($providers as $provider)
                                        <tr>
                                            <th>
                                                {{ $provider->name }}
                                            </th>   
                                            <td>
                                                @if($provider->collections > 0)
                                                    <a href="{{ action('ProviderController@collection', $provider->id) }}">{{ $provider->collections }}</a>
                                                @else
                                                    {{ $provider->collections }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($provider->datasets > 0)
                                                    <a href="{{ action('ProviderController@dataset', $provider->id) }}">{{ $provider->datasets }}</a>
                                                @else
                                                    {{ $provider->datasets }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($provider->databases > 0)
                                                    <a href="{{ action('ProviderController@database', $provider->id) }}">{{ $provider->databases }}</a>
                                                @else
                                                    {{ $provider->databases }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>                                
                            </div>	
                        </div>	<!-- // row -->	
                        <br/>
                        <div class="row">
                            <div class="col-md-12">
                                <p>
                                    <a href="{{ action('ProviderController@index') }}" class="btn btn-sm btn-primary pull-right">More</a>
                                </p> 
                            </div>
                        </div>	
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->

        </div>
    </section><!-- /.content -->                
</aside><!-- /.right-side -->
@endsection