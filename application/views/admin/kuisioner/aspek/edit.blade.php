@extends('admin.layout')

@section('title', 'Administrator | Teknik Informatika Untag Surabaya')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <ol class="breadcrumb breadcrumb-col-teal">
                <li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                <li><a href="{{route('admin.kuisioner.aspek.index')}}">Aspek Kuisioner</a></li>
                <li><a class="active" href="#">Edit</a></li>
            </ol>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <?php echo form_open_multipart(route('admin.kuisioner.aspek.update'),'id="form_validation" novalidate="novalidate"')?>
                        <div class="header">
                            <h2>EDIT ASPEK KUISIONER</h2>
                        </div>
                        <div class="body">
                            <label for="email_address">Nama Aspek Kuisioner</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input value="{{isset($aspek_kuisioner->nama_aspek) ? $aspek_kuisioner->nama_aspek : ''}}" type="text" class="form-control" name="nama_aspek" required="" aria-required="true">
                                </div>
                            </div>
                            <input type="text" class="hidden" name="id" value="{{isset($aspek_kuisioner->id) ? $aspek_kuisioner->id : ''}}">
                            <button type="submit" class="btn bg-green waves-effect simpan" id="simpan">SIMPAN</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('additional-scripts')
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

        
    </script>
    
@endsection