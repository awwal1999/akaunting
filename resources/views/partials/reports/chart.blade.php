@php $chart = $class->getChart(); @endphp

<div id="report-chart" class="card-body pt-0">
    {!! $chart->container() !!}
</div>

@push('charts')
    <script>
        var cash_flow = new Vue({
            el: '#report-chart',
        });
    </script>
@endpush

@push('body_scripts')
    {!! $chart->script() !!}
@endpush
