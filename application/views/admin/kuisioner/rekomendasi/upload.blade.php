@extends('admin.layout')

@section('title', 'Administrator | Teknik Informatika Untag Surabaya')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <ol class="breadcrumb breadcrumb-col-teal">
                <li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                <li><a href="{{route('admin.kuisioner.rekomendasi.index')}}">Pertanyaan Kuisioner</a></li>
                <li><a class="active" href="#">Upload</a></li>
            </ol>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <form id="form_validation" novalidate="novalidate">
                        <div class="header">
                            <h2>UPLOAD REKOMENDASI KUISIONER</h2>
                        </div>
                        <div class="body">
                            <label for="email_address">File Rekomendasi Kuisioner</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="file" class="form-control" name="file_rekomendasi" id="myfile" required="" aria-required="true">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="progress" hidden>
                                    <div class="progress-bar myprogress" role="progressbar" style="width:0%">0%</div>
                                </div>
                                <p class="font-italic col-orange msg"></p>
                            </div>
                            <button type="button" id="btn" class="btn bg-green waves-effect simpan" id="simpan">UPLOAD</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<input type="text" hidden name="token_csrf" value="<?php echo $csrf["token"]?>">
<input type="text" hidden name="name_csrf" value="<?php echo $csrf["name"]?>">
@stop
@section('additional-scripts')
    <!-- Bootstrap Notify Plugin Js -->
    <script src="{{base_url('assets/plugins/bootstrap-notify/bootstrap-notify.js')}}"></script>
    <script src="{{base_url('assets/js/custom-admin.js')}}"></script>
    <?php
        if(isset($_SESSION['message'])){
            if($_SESSION['message'] === 'success'){
                if($_SESSION['method'] === 'save'){
                    echo "<script>showNotif('".$_SESSION['pesan']."','success')</script>";
                }else{
                    echo "<script>showNotif('".$_SESSION['pesan']."','success')</script>";
                }
            }else{
                if($_SESSION['method'] === 'save'){
                    echo "<script>showNotif('".$_SESSION['pesan']."','error')</script>";
                }else{
                    echo "<script>showNotif('".$_SESSION['pesan']."','error')</script>";
                }
            }
        }
    ?>
    <script>
        $('#form_validation').validate({
            highlight: function (input) {
                $(input).parents('.form-line').addClass('error');
            },
            unhighlight: function (input) {
                $(input).parents('.form-line').removeClass('error');
            },
            errorPlacement: function (error, element) {
                $(element).parents('.form-group').append(error);
            }
        });

        $(function () {
            $('#btn').click(function () {
                let myfile = $('#myfile').val();
                if (myfile == '') {
                    showNotif('Silakan Pilih File','error');
                    return;
                }
                $('.progress').attr('hidden',false);
                $('.myprogress').css('width', '0');
                $('.msg').text('');
                let csrfName = $('[name=name_csrf]').val();
                let csrfToken = $('[name=token_csrf]').val();
                var formData = new FormData();
                formData.append('file_rekomendasi', $('#myfile')[0].files[0]);
                formData.append([csrfName],csrfToken);
                $('#btn').attr('disabled', 'disabled');
                $('.msg').text('Uploading in progress...');
                $.ajax({
                    url: '<?php echo route("admin.kuisioner.rekomendasi.upload.proses")?>',
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    dataType:'JSON',
                    // this part is progress bar
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                percentComplete = parseInt(percentComplete * 100);
                                $('.myprogress').text(percentComplete + '%');
                                $('.myprogress').css('width', percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function (response) {
                        $('[name=token_csrf]').val(response.csrf.token);
                        $('[name=name_csrf]').val(response.csrf.name);
                        $('.msg').text(response.pesan);
                        showNotif(response.pesan,response.message);
                        $('#btn').removeAttr('disabled');
                        $('.progress').attr('hidden',true);
                        setTimeout(() => {
                            $('.msg').attr('hidden',true);
                            $('#form_validation')[0].reset();
                        }, 2000);
                    }
                });
            });
        });
    </script>
    
@endsection