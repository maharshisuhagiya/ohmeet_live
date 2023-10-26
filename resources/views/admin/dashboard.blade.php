@extends('admin.layout')

@section('content')
{{--    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Home</a></li>
            </ol>
        </div>
    </div>--}}
    <!-- row -->
    <?php $page_id = \App\Models\ProjectPage::where('route_url',\Illuminate\Support\Facades\Route::currentRouteName())->pluck('id')->first(); ?>
    @if(getUSerRole()==1 || (getUSerRole()!=1 && is_read($page_id)) )
   
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="container-fluid mt-3">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6">
                                <div class="card gradient-1">
                                    <div class="card-body">
                                        <h3 class="card-title text-white">Today Orders</h3>
                                        <div class="d-inline-block">
                                            <h2 class="text-white" id="todayPurchaseCoin"> {{ $todayPurchaseCoin }} </h2>
                                            <p class="text-white mb-0">Yesterday Orders</p>
                                            <h4 class="text-white mb-0"> {{ $yesterdayPurchaseCoin }} </h4>
                                        </div>
                                        <span class="float-right display-5 opacity-5"><i class="fa fa-shopping-cart"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="card gradient-2">
                                    <div class="card-body">
                                        <h3 class="card-title text-white">Today Sales</h3>
                                        <div class="d-inline-block">
                                            <h2 class="text-white" id="todayAmount">₹ {{ $todayAmount }} </h2>
                                            <p class="text-white mb-0">Yesterday Sales</p>
                                            <h4 class="text-white mb-0">₹ {{ $yesterdayAmount }} </h4>
                                        </div>
                                        <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-lg-3 col-sm-6">
                                <div class="card gradient-3">
                                    <div class="card-body">
                                        <h3 class="card-title text-white">Today Inquiry</h3>
                                        <div class="d-inline-block">
                                            <h2 class="text-white"> 50000 </h2>
                                            <p class="text-white mb-0">Today Opinion</p>
                                            <h4 class="text-white mb-0"> 532532 </h4>
                                        </div>
                                        <span class="float-right display-5 opacity-5"><i class="fa fa-heart"></i></span>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-lg-3 col-sm-6">
                                <div class="card gradient-4">
                                    <div class="card-body">
                                        <h3 class="card-title text-white">Today Users</h3>
                                        <div class="d-inline-block">
                                            <h2 class="text-white" id="todayUser"> {{ $todayUser }} </h2>
                                            <p class="text-white mb-0"> Yesterday Users</p>
                                            <h4 class="text-white mb-0"> {{ $yesterdayUser }} </h4>
                                        </div>
                                        <span class="float-right display-5 opacity-5"><i class="fa fa-users"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group ">
                                    <div class="input-group">
                                        <input type="text" class="form-control custom_date_picker" id="date_search" name="date_search" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" data-date-end-date="0d"> <span class="input-group-append"><span class="input-group-text"><i class="mdi mdi-calendar-check"></i></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>       
            </div>

    </div>

  
    
    @endif
@endsection
@section('js')
    <!-- user list JS start -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#date_search').change(function(){
            var date = $('#date_search').val();
            $.ajax({
                type: 'POST',
                url: "{{ url('admin/date-wise-search') }}",
                data: { 
                    date: date
                },
                success: function (res) {
                    $('#todayPurchaseCoin').html(res.data.todayPurchaseCoin);
                    $('#todayAmount').html(res.data.todayAmount);
                    $('#todayUser').html(res.data.todayUser);
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });
    </script>
@endsection
