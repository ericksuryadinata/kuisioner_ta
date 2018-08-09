@extends('website.layout')

@section('title', 'Kuisioner | Teknik Informatika Untag Surabaya')

@section('content')
<section class="content">
    <div class="container">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="body bg-green">
                        <h1 class="text-center">Terima Kasih Telah Mengikuti Kuisioner Kami</h1>
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
    margin: 250px 15px 0 15px;
}
</style>
@endsection

@section('additional-scripts')
<script>
    let base_url = '<?php echo site_url();?>';
var sweetAlert = function(title, message, status, wait = 10000, timer = 5000, isReload = false){
    setTimeout(() => {
        swal({
            title   : title,
            text    : message + '<br/>Anda dialihkan dalam waktu <strong class="swal-timer-count">' + timer/1000 + '</strong> detik...',
            type    : status,
            html    : true,
            timer   : timer,
            allowEscapeKey  : false
        }, function () {
            swal.close();
            if(isReload)
                window.location = base_url+'sign/out';
        });
        var e = $(".sweet-alert").find(".swal-timer-count");
        var n = +e.text();
        setInterval(function(){
            n > 1 && e.text (--n);
        }, 1000);
    }, wait);
}

$(document).ready(function () {
    sweetAlert('Terima Kasih', 'Anda Telah Mengikuti Kuisioner Kami', 'success', 5000,3000, true);
});
</script>
@endsection
