@extends('admin.layout')

@section('title', 'Administrator | Teknik Informatika Untag Surabaya')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <ol class="breadcrumb breadcrumb-col-teal">
                <li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                <li><a href="{{route('admin.kuisioner.pertanyaan.index')}}">Pertanyaan Kuisioner</a></li>
                <li><a class="active" href="#">Tambah</a></li>
            </ol>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <?php echo form_open_multipart(route('admin.kuisioner.pertanyaan.save'),'id="form_validation" novalidate="novalidate"')?>
                        <div class="header">
                            <h2>TAMBAH PERTANYAAN KUISIONER</h2>
                        </div>
                        <div class="body">
                            <label for="email_address">Pertanyaan Kuisioner</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea name="nama_pertanyaan" id="nama_pertanyaan" required="" aria-required="true"></textarea>
                                </div>
                            </div>
                            <label for="email_address">Aspek Kuisioner</label>
                            <div class="form-group">
                                <select class="form-control show-tick" name="id_aspek" required="" aria-required="true" >
                                    <option value="">-- Pilih --</option>
                                    @foreach ($aspek as $key => $value)
                                        <option value="{{$value->id}}">{{$value->nama_aspek}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn bg-green waves-effect simpan" id="simpan">SIMPAN</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('additional-styles')
    <link href="{{base_url('assets/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />    
@endsection

@section('additional-scripts')
    <!-- TinyMCE -->
    <script src="{{base_url('assets/plugins/tinymce/tinymce.js')}}"></script>
    <script>
        
        //TinyMCE
        tinymce.init({
            selector: "textarea#nama_pertanyaan",
            theme: "modern",
            height: 300,
            plugins: [
                'advlist autolink lists link charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern imagetools'
            ],
            toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
            toolbar2: 'print preview | forecolor backcolor emoticons',
        });

        tinymce.suffix = ".min";
        tinyMCE.baseURL = "{{base_url('assets/plugins/tinymce')}}";
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