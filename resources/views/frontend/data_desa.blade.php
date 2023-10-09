@extends('frontend.fe_layout.main')

@section('content')
    <style>
        .banner-area {
            background-image: url('{{ asset($_setting['img_header']) }}')
        }


        .chart-legend li span{
            display: inline-block;
            width: 12px;
            height: 12px;
            margin-right: 5px;
        }

        .chart-legend ul{
            list-style: disclosure-closed !important;
            padding-left: 15px;
        }

        .chart-legend li{
            cursor: default;
        }
        
        .chart-legend li:hover{
            font-weight: 600;
        }

        .chart-legend li.hide{
            text-decoration:line-through;
            text-decoration-thickness:2px;
        }
        
       
    </style>
    <!-- start banner Area -->
    <!-- start banner Area -->
    <section class="banner-area relative about-banner" id="home">
        <div class="overlay overlay-bg"></div>
        <div class="container">
            <div class="row d-flex align-items-center justify-content-center">
                <div class="about-content col-lg-12">
                    <h1 class="text-white">
                        {{$data['nama']}}
                    </h1>
                    <p class="text-white link-nav"><a href="/" class="my-loading">Beranda </a> <span class="lnr lnr-arrow-right"></span> <a
                            href="" class="my-loading">{{$data['nama']}}</a></p>
                </div>
            </div>
        </div>
    </section>
    <br>
    <section class="course-mission-area pb-120">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="menu-content pb-70 col-lg-8">
                    <div class="title text-center">
                        <h1 class="mb-10"></h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5">
                    
                    <canvas id="chart" height="200" ></canvas>
                    <div id="js-legend" class="chart-legend" style="margin-top:5px"></div>
                    <div id="pagination" style="margin:10px 0"></div>

                </div>
                <div class="col-lg-7">
                    <div id="peta_wilayah" class="table-responsive" style="background: #fff;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th rowspan="2" scope="col">Kelompok</th>
                                    <th colspan="2" scope="col">Jumlah</th>
                                    <th colspan="2" scope="col">Laki-laki</th>
                                    <th colspan="2" scope="col">Perempuan</th>
                                </tr>
                                <tr>
                                    <th>n</th>
                                    <th>%</th>
                                    <th>n</th>
                                    <th>%</th>
                                    <th>n</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center"> <span class="fa fa-spin fa-spinner"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.2/jquery.twbsPagination.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
 
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/randomcolor/0.6.1/randomColor.min.js"
        integrity="sha512-vPeZ7JCboHcfpqSx5ZD+/jpEhS4JpXxfz9orSvAPPj0EKUVShU2tgy7XkU+oujBJKnWmu4hU7r9MMQNWPfXsYw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        chart_data = {};

        my_chart = new Chart('chart', {
            type: 'pie',
            data: {},
            options: {
                legend: {
                    position: "bottom",
                    display : {{ request()->segment(2) == 'pekerjaan' ? 'false' : 'true' }}

                },
                responsive: true,
                plugins: {
                }
            },
        });
        

        $(function() {

            var path = window.location.pathname; // get the path, e.g. "/data/gol_darah"
            var value = path.split('/').pop(); // extract the last segment, e.g. "gol_darah"

            $.post("{{ route('data_desa.ajax', ['tipe' => ':tipe']) }}".replace(':tipe', value), function(e) {
                    if (!e.status) return $('#peta_wilayah tbody td').html('Belum ada data');
                    let dt = e.data.item.map(x => `
            <tr class="incTbl">
                
                <td>${x.kelompok || ''}</td>
                <td>${x.jumlah || ''}</td>
                <td>${x.percent_all || '0'} %</td>
                <td>${x.lk || ''}</td>
                <td>${x.percent_lk || '0'} %</td>
                <td>${x.pr  || ''} </td>
                <td>${x.percent_pr || '0'} %</td>
            </tr>
        `).join('');

                    dt += `
            <tr>
                <th></th>
                <th>${e.data.total.jumlah || ''}</th>
                <th>${parseFloat(e.data.total.percent_all.toFixed(2)) || '0'} %</th>
                <th>${e.data.total.lk || ''}</th>
                <th>${parseFloat(e.data.total.percent_lk.toFixed(2)) || '0'} %</th>
                <th>${e.data.total.pr || ''}</th>
                <th>${parseFloat(e.data.total.percent_pr.toFixed(2)) || '0'} %</th>
            </tr>
        `;
                    $('#peta_wilayah tbody').html(dt);

                    chart_data = e.data.chart;
                    my_chart.data = {
                        labels: chart_data.labels,
                        datasets: [{
                            data: chart_data.jumlah,
                            backgroundColor: []
                        }]
                    };
                    $.each(my_chart.data.datasets[0].data, function(index, value) {
                        var color = randomColor();
                        my_chart.data.datasets[0].backgroundColor.push(color);
                    });
                    my_chart.update();

                    if(e.data.type == 'pekerjaan'){
                        $('#js-legend').html(my_chart.generateLegend());
                        
                        data_item = $('.chart-legend li');
                        
                        chart_pagination( data_item );

                        data_item.click(function (e) { 
                            e.preventDefault();
                            
                            let hidden = $(this).hasClass('hide');
                            if(hidden) $(this).removeClass('hide');
                            else $(this).addClass('hide');

                            my_chart.getDatasetMeta(0).data[$(this).index()].hidden = !hidden
                            my_chart.update();
                            
                        });

                    }

                }, "json")
                .fail(e => $('#peta_wilayah tbody td').html('Tidak bisa mengambil data'))
        });
        
        function chart_pagination(data_item){

            itemsPerPage = 5;
            totalPages = Math.ceil(data_item.length / itemsPerPage);
            displayData = (pageNumber) => {
                let startIndex = (pageNumber - 1) * itemsPerPage;
                let endIndex = startIndex + itemsPerPage;
                data_item.hide().slice(startIndex, endIndex).show();

            }

            displayData(1);

            $('#pagination').twbsPagination({
                totalPages: totalPages,
                visiblePages: itemsPerPage,
                first: '<<',
                prev: '<',
                next: '>',
                last: '>>',
                onPageClick: function(event, page) {
                    displayData(page);    
                }
            });

        }



    </script>
@endsection
{{-- @push('js') --}}
{{-- @endpush --}}
