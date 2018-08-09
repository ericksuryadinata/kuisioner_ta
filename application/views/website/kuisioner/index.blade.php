@extends('website.layout')

@section('title', 'Kuisioner | Teknik Informatika Untag Surabaya')

@section('content')
<section class="content">
    <div class="container">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>ASPEK {{$aspek}}</h2>
                    </div>
                    <div class="body">
                        <?php echo form_open(route('home.kuisioner.save'),'id="wizard_with_validation"')?>
                        <input type="text" value="<?php echo count($soal)?>" name="banyak-soal" hidden>
                        @foreach ($soal as $key => $value)
                            <h3>Pertanyaan {{$key + 1}}</h3>
                            <fieldset>
                                <p>{{$value->pertanyaan}}</p>
                                @for ($i = 1; $i <= 5; $i++)
                                    <input name="jawaban-q{{$key}}" value="{{$i}}" type="radio" id="jawaban{{$key.$i}}" class="with-gap radio-col-red" required aria-required="true">
                                    <label for="jawaban{{$key.$i}}">{{$i}}</label>
                                @endfor
                                <p class="font-italic col-pink"><small>* 1. Sangat Tidak Setuju</small></p>
                                <p class="font-italic col-pink"><small>* 2. Tidak Setuju</small></p>
                                <p class="font-italic col-pink"><small>* 3. Cukup Setuju</small></p>
                                <p class="font-italic col-pink"><small>* 4. Setuju</small></p>
                                <p class="font-italic col-pink"><small>* 5. Sangat Setuju</small></p>
                            </fieldset>
                        @endforeach
                        <?php echo form_close()?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('additional-styles')
<style>
section.content{
    margin: 100px 15px 0 15px;
}
</style>
@endsection

@section('additional-scripts')
<script>
    $(function () {
        //Advanced form with validation
        var form = $('#wizard_with_validation').show();
        let answer;
        let banyakSoal = <?php echo count($soal)?>;
        form.steps({
            headerTag: 'h3',
            bodyTag: 'fieldset',
            transitionEffect: 'slideLeft',
            onInit: function (event, currentIndex) {
                $.AdminBSB.input.activate();

                //Set tab width
                var $tab = $(event.currentTarget).find('ul[role="tablist"] li');
                var tabCount = $tab.length;
                $tab.css('width', (100 / tabCount) + '%');
                answer = [];
                //set button waves effect
                setButtonWavesEffect(event);
            },
            onStepChanging: function (event, currentIndex, newIndex) {
                if (currentIndex > newIndex) { return true; }

                if (currentIndex < newIndex) {
                    form.find('.body:eq(' + newIndex + ') label.error').remove();
                    form.find('.body:eq(' + newIndex + ') .error').removeClass('error');
                }

                form.validate().settings.ignore = ':disabled,:hidden';
                return form.valid();
            },
            onStepChanged: function (event, currentIndex, priorIndex) {
                setButtonWavesEffect(event);
            },
            onFinishing: function (event, currentIndex) {
                form.validate().settings.ignore = ':disabled';
                return form.valid();
            },
            onFinished: function (event, currentIndex) {
                if(answer.length != banyakSoal){
                    for (let index = 0; index < banyakSoal; index++) {
                        answer.push($("[name='jawaban-q"+index+"']:checked").val());    
                    }
                }
                console.log(answer);
                $('#wizard_with_validation').append('<input type="text" name="final" value="'+answer+'" hidden>');
                console.log($('#wizard_with_validation').serialize());
                $('#wizard_with_validation').submit();
            }
        });

        form.validate({
            highlight: function (input) {
                $(input).parents('.form-line').addClass('error');
            },
            unhighlight: function (input) {
                $(input).parents('.form-line').removeClass('error');
            },
            errorPlacement: function (error, element) {
                $(element).parents('.form-group').append(error);
            },
            rules: {
                'confirm': {
                    equalTo: '#password'
                }
            }
        });
    });

    function setButtonWavesEffect(event) {
        $(event.currentTarget).find('[role="menu"] li a').removeClass('waves-effect');
        $(event.currentTarget).find('[role="menu"] li:not(.disabled) a').addClass('waves-effect');
    }
</script>
@endsection