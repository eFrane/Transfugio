@if (is_array($property->getType()))
    @foreach ($property->getType() as $type)
        @include ('transfugio::api.schema.type', compact('type'))
    @endforeach
@else
    @include ('transfugio::api.schema.type', ['type' => $property->getType()])
@endif
