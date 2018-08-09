@extends('admin.layout')

@section('title', 'Administrator | Teknik Informatika Untag Surabaya')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
        <ol class="breadcrumb breadcrumb-col-teal">
            <li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
            <li><a class="active" href="#">ASPEK KUISIONER</a></li>
        </ol>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>LIST ASPEK KUISIONER</h2>
                        <br>
                        <a href="{{route('admin.kuisioner.aspek.create')}}" class="btn bg-indigo waves-effect">Tambah Data</a>
                        <a href="javascript:void(0)" class="btn bg-green waves-effect" id="refresh">Refresh</a>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover js-basic-example dataTable" id="kuisioner-aspek-list">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Aspek</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Aspek</th>
                                        <th>Aksi</th>
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

        table = $('#kuisioner-aspek-list').DataTable({
            serverSide: true,
            responsive:true,
            order: [],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ajax: {
                url: '{{route("admin.kuisioner.aspek.datatable")}}',
                type: 'GET',
                data:{[csrfName]:csrfToken}
            },
        });

        $('#refresh').on('click', function (e) {
            e.preventDefault();
            table.ajax.reload(null,false);
        });

        $('.js-basic-example').on('click','.hapus', function () {
            let id = $(this).data('id');
            let csrfName = $('[name=name_csrf]').val();
            let csrfToken = $('[name=token_csrf]').val();
            $.ajax({
                type: "POST",
                url: '{{route("admin.kuisioner.aspek.delete")}}',
                data: {'id':id,[csrfName]:csrfToken},
                dataType: "JSON",
                success: function (response) {
                    if(response.message === 'success'){
                        $('[name=token_csrf]').val(response.csrf.token);
                        $('[name=name_csrf]').val(response.csrf.name);
                        $('#refresh').trigger('click');
                        showNotif(response.pesan,response.message);
                    }else{
                        $('[name=token_csrf]').val(response.csrf.token);
                        $('[name=name_csrf]').val(response.csrf.name);
                        $('#refresh').trigger('click');
                        showNotif(response.pesan,response.message);
                    }
                }
            });
        });

    });
    </script>
@endsection