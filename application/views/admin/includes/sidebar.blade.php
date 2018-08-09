<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <div class="user-info" style="height:50px;padding:5px 5px 5px 15px;">
            <div class="info-container" style="top:1px">
                <div class="name">{{$ctrl->surename}}</div>
                <div class="email">{{$ctrl->email}}</div>
            </div>
        </div>
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">MAIN NAVIGATION</li>
                <li class="{{isset($active_dashboard) ? $active_dashboard : ''}}">
                    <a href="{{route('admin.dashboard')}}">
                        <i class="material-icons">dashboard</i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{isset($active_kuisioner) ? $active_kuisioner : ''}}">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">border_color</i>
                        <span>Kuisioner</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="{{isset($active_kuisioner_aspek) ? $active_kuisioner_aspek : ''}}">
                            <a href="{{route('admin.kuisioner.aspek.index')}}">Aspek Kuisioner</a>
                        </li>
                        <li class="{{isset($active_kuisioner_pertanyaan) ? $active_kuisioner_pertanyaan : ''}}">
                            <a href="{{route('admin.kuisioner.pertanyaan.index')}}">Pertanyaan Kuisioner</a>
                        </li>
                        <li class="{{isset($active_kuisioner_rekomendasi) ? $active_kuisioner_rekomendasi : ''}}">
                            <a href="{{route('admin.kuisioner.rekomendasi.index')}}">Rekomendasi Kuisioner</a>
                        </li>
                    </ul>
                </li>
                <li class="{{isset($active_responden) ? $active_responden : ''}}">
                    <a href="{{route('admin.responden.index')}}">
                        <i class="material-icons">people</i>
                        <span>Data Responden</span>
                    </a>
                </li>
                <li class="{{isset($active_assessment) ? $active_assessment : ''}}">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">assessment</i>
                        <span>Assessment</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="{{isset($active_assessment_tabel) ? $active_assessment_tabel : ''}}">
                            <a href="{{route('admin.assessment.tabel')}}">Tabel Hasil Kuisioner</a>
                        </li>
                        <li class="{{isset($active_assessment_grafik) ? $active_assessment_grafik : ''}}">
                            <a href="{{route('admin.assessment.grafik')}}">Grafik Hasil Kuisioner</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- #Menu -->
        <!-- Footer -->
        <div class="legal">
            <div class="copyright">
                &copy; 2017 - 2018 <a href="#">Badan Kuisioner</a>.
            </div>
        </div>
        <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->
</section>