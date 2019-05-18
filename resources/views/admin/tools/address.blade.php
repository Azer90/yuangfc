<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id['address']}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="row">
            <div class="col-md-7">
                <input id="{{$id['address']}}" name="{{$name['address']}}" class="form-control" value="{{ old($column['address'], $value['address']) }}" {!! $attributes !!} />
                <span id="search">search</span>
            </div>
            <div class="col-md-3" >
                <input id="{{$id['lng']}}" name="{{$name['lng']}}" class="form-control" value="{{ old($column['lng'], $value['lng']) }}" {!! $attributes !!} />
            </div>
            <div class="col-md-3" >
                <input id="{{$id['lat']}}" name="{{$name['lat']}}" class="form-control" value="{{ old($column['lat'], $value['lat']) }}" {!! $attributes !!} />
            </div>
        </div>

        <br>

        <div id="map_{{$id['address']}}" style="width: 100%;height: {{ $height }}px"></div>

        @include('admin::form.help-block')

    </div>
</div>
