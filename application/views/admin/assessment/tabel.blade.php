@extends('admin.layout')

@section('title', 'Administrator | Teknik Informatika Untag Surabaya')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
        <ol class="breadcrumb breadcrumb-col-teal">
            <li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
            <li><a class="active" href="#">tabel kuisioner</a></li>
        </ol>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>LIST HASIL KUISIONER</h2>
                        <br>
                        <a href="{{route('admin.assessment.tabel.upload')}}" class="btn bg-orange waves-effect">Upload Data Assessment</a>
                        <a href="javascript:void(0)" class="btn bg-green waves-effect" id="refresh">Refresh</a>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover js-basic-example dataTable" id="assessment">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Responden</th>
                                        <th>Pertanyaan</th>
                                        <th>Jawaban</th>
                                        <th>Aspek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Responden</th>
                                        <th>Pertanyaan</th>
                                        <th>Jawaban</th>
                                        <th>Aspek</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
    let table;
    $(document).ready(function () {
        let csrfName = $('[name=name_csrf]').val();
        let csrfToken = $('[name=token_csrf]').val();

        table = $('#assessment').DataTable({
            serverSide: true,
            responsive:true,
            order: [],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ajax: {
                url: '{{route("admin.assessment.datatable")}}',
                type: 'GET',
                data:{[csrfName]:csrfToken}
            },
        });

        $('#refresh').on('click', function (e) {
            e.preventDefault();
            table.ajax.reload(null,false);
        });

    });
    </script>
@endsection