@extends('admin.layout')

@section('title', 'Administrator | Teknik Informatika Untag Surabaya')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
        <ol class="breadcrumb breadcrumb-col-teal">
            <li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
            <li><a class="active" href="#">grafik kuisioner</a></li>
        </ol>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>RADAR CHART</h2>
                        <br>
                        @if (count($result) == 0)

                        @else
                            <label for="email_address">Aspek Kuisioner</label>
                            <select class="form-control show-tick" id="id_aspek" name="id_aspek" required="" aria-required="true" >
                                <option value="all" selected>SEMUA ASPEK</option>
                                @foreach ($aspek as $key => $value)
                                    <option value="{{$value->id}}">{{$value->nama_aspek}}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="body">
                        @if (count($result) == 0)
                            <h2 id="label" class="text-center m-t-0 m-b-15">TIDAK ADA DATA</h2>
                        @else
                            <h2 id="label" class="text-center m-t-0 m-b-15">SEMUA ASPEK</h2>
                            <div class="row" id="canvas-wrapper">
                                <?php
                                if(count($result) < 4){
                                    for ($i=0; $i <count($result); $i++) {
                                        $w = floor(12/count($result));
                                        echo '<div class="col-md-'.$w.'"><canvas id="radar_chart_'.$i.'" height="200"></canvas><div class="col-md-12"></div></div>';
                                    }
                                }else{
                                    for ($i=0; $i <count($result); $i++) { 
                                        echo '<div class="col-md-4"><canvas id="radar_chart_'.$i.'" height="200"></canvas><div class="col-md-12"></div></div>';
                                    }
                                }
                                    
                                ?>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<input type="text" hidden name="token_csrf" value="<?php echo $csrf["token"]?>">
<input type="text" hidden name="name_csrf" value="<?php echo $csrf["name"]?>">
@stop
@section('additional-styles')
    <link href="{{base_url('assets/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />    
@endsection
@section('additional-scripts')
    <script>
        let table;
        let dataFromDatabase = <?php echo json_encode($result)?>;
        function random_rgba() {
            var o = Math.round, r = Math.random, s = 255;
            return 'rgba(' + o(r()*s) + ',' + o(r()*s) + ',' + o(r()*s) + ',' + r().toFixed(1) + ')';
        }

        function radarChart(result,index) {
            // console.log(result);
            let config = null;
            let dataset_parse = [];
            label_dataset = 'Kuisioner '+(index+1);
            let dataparse = [];
            result.data.forEach(element => {
                dataparse.push(parseFloat(element).toFixed(2))
            });
            dataset_parse.push({
                label:label_dataset,
                data:dataparse,
                borderColor: random_rgba(),
                backgroundColor: random_rgba(),
                pointBorderColor: random_rgba(),
                pointBackgroundColor: random_rgba(),
                pointBorderWidth: 1
            });
            // console.log(dataset_parse);
            config = {
                type: 'radar',
                data: {
                    labels: result.label,
                    datasets: dataset_parse
                },
                options: {
                    responsive: true,
                    legend: {
                        display: true,
                        labels: {
                            fontColor: 'rgb(255, 99, 132)'
                        }
                    }
                }
            }
            return config;
        }

        function create_recommendation(response){
            let html = '';
            html += '<div class="col-md-12 m-t-30">';
            response.forEach(element => {
                html += '<p>'+element.id_pertanyaan+', rekomendasi :'+element.rekomendasi+'</p>';
            });
            html += '</div>';
            return html;
        }

        $(document).ready(function () {
            let csrfName = $('[name=name_csrf]').val();
            let csrfToken = $('[name=token_csrf]').val();

            $("#id_aspek").on('change', function () {
                let method = $('[name="id_aspek"] :selected').val();
                $.ajax({
                    type: "GET",
                    url: "<?php echo route('admin.assessment.dataGrafik')?>",
                    data:{[csrfName]:csrfToken,aspek:method},
                    dataType: "JSON",
                    success: function (response) {
                        console.log(response);
                        $('[name=token_csrf]').val(response.csrf.token);
                        $('[name=name_csrf]').val(response.csrf.name);
                        $("#label").text(response.result.judul);
                        $("#canvas-wrapper").empty();
                        let appendCanvas = '';
                        let resultLength = (Object.keys(response.result).length - 1);
                        if(resultLength < 4){
                            // console.log(Object.keys(response.result).length - 1);
                            for (let i= 0; i < resultLength; i++) {
                                let w = Math.floor(12/resultLength);
                                if(response.rek[i].length != 0){
                                    $("#canvas-wrapper").append('<div class="col-md-'+w+'"><canvas id="radar_chart_'+i+'" height="200"></canvas>'+create_recommendation(response.rek[i])+'</div>');
                                }else{
                                    $("#canvas-wrapper").append('<div class="col-md-'+w+'"><canvas id="radar_chart_'+i+'" height="200"></canvas></div>');
                                }
                                new Chart(document.getElementById("radar_chart_"+i).getContext("2d"),radarChart(response.result[i],i));
                            }
                            
                        }else{
                            for (let i=0; i <resultLength; i++) {
                                if(response.rek[i].length != 0){
                                    $("#canvas-wrapper").append('<div class="col-md-'+w+'"><canvas id="radar_chart_'+i+'" height="200"></canvas>'+create_recommendation(response.rek[i])+'</div>');
                                }else{
                                    $("#canvas-wrapper").append('<div class="col-md-'+w+'"><canvas id="radar_chart_'+i+'" height="200"></canvas></div>');
                                }
                                new Chart(document.getElementById("radar_chart_"+i).getContext("2d"),radarChart(response.result[i],i));
                            }
                        }
                    }
                });
            });
            for (let index = 0; index < dataFromDatabase.length; index++) {
                new Chart(document.getElementById("radar_chart_"+index).getContext("2d"),radarChart(dataFromDatabase[index],index));
            }
        });
    </script>
@endsection