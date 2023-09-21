@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">User List</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        {{-- <div class="action-section">
                            <div class="d-flex">
                            <?php $page_id = \App\Models\ProjectPage::where('route_url','admin.enduser.list')->pluck('id')->first(); ?>
                            @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                <button type="button" class="btn btn-primary" id="AddEndUserBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            @endif
                            </div>
                        </div> --}}

                        @if(isset($action) && $action=='list')
                        <div class="custom-tab-1">
                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item host_user_page_tabs" data-tab="all_host_user_tab"><a class="nav-link active show" data-toggle="tab" href="">All</a>
                                </li>
                        
                                <li class="nav-item host_user_page_tabs" data-tab="active_user_tab"><a class="nav-link" data-toggle="tab" href="">Active</a>
                                </li>
                                <li class="nav-item host_user_page_tabs" data-tab="deactive_user_tab"><a class="nav-link" data-toggle="tab" href="">Deactive</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="all_host_user_tab">
                            <table id="all_end_users" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Profile</th>
                                    <th>Contact Info</th>
                                    <th>Other Info</th>
                                    <th>User</th>
                                    <th>User Status</th>
                                    <th>Registration Date</th>
                                    {{-- <th>Other</th> --}}
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Profile</th>
                                    <th>Contact Info</th>
                                    <th>Other Info</th>
                                    <th>User</th>
                                    <th>User Status</th>
                                    <th>Registration Date</th>
                                    {{-- <th>Other</th> --}}
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        @endif

                        {{-- @if(isset($action) && $action=='create')
                            @include('admin.end_users.create')
                        @endif

                        @if(isset($action) && $action=='edit')
                            @include('admin.end_users.create')
                        @endif --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ asset('js/UserImgJs.js') }}" type="text/javascript"></script>
<!-- end_user list JS start -->
<script type="text/javascript">

$(document).ready(function() {
    host_user_page_tabs('',true);
});

function get_end_users_page_tabType() {
    var tab_type;
    $('.host_user_page_tabs').each(function() {
        var thi = $(this);
        if($(thi).find('a').hasClass('show')){
            tab_type = $(thi).attr('data-tab');
        }
    });
    return tab_type;
}

function host_user_page_tabs(tab_type='',is_clearState=false) {
    if(is_clearState){
        $('#all_end_users').DataTable().state.clear();
    }
    var agency_search = $('#agency_host_user_tab').val();

    $('#tab_type_export').val(tab_type);
    $('#agency_search_export').val(agency_search);

    $('#all_end_users').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        "lengthMenu": [10, 25, 50, 100, 500, 1000],
        'stateSave': function(){
            if(is_clearState){
                return false;
            }
            else{
                return true;
            }
        },
        "ajax":{
            "url": "{{ url('admin/alluserlist') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type, agency_search: agency_search},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "50px", "targets": 0 },
            { "width": "145px", "targets": 1 },
            { "width": "145px", "targets": 2 },
            { "width": "200px", "targets": 3 },
            { "width": "50px", "targets": 4 },
            { "width": "120px", "targets": 5 },
            { "width": "120px", "targets": 6 },
            // { "width": "180px", "targets": 7 },
        ],
        "columns": [
            {data: 'id', name: 'id', class: "text-center", orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'profile_pic', name: 'profile_pic', class: "text-center multirow"},
            {data: 'contact_info', name: 'contact_info', class: "text-left multirow", orderable: false},
            {data: 'login_info', name: 'login_info', class: "text-left multirow", orderable: false},
            {data: 'user', name: 'user', class: "text-left multirow", orderable: false},
            {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
            {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
            // {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
        ]
    });
}

$(".host_user_page_tabs").click(function() {
    var tab_type = $(this).attr('data-tab');
    host_user_page_tabs(tab_type,true);
});

$(".host_user_page_tabs_agency").change(function() {
    var tab_type = get_end_users_page_tabType();
    host_user_page_tabs(tab_type, true);
});

function changeUserStatus(user_id) {
    var tab_type = get_end_users_page_tabType();
     
    $.ajax({
        type: 'GET',
        url: "{{ url('admin/changeuserstatus') }}" +'/' + user_id,
        success: function (res) {
            if(res.status == 200 && res.action=='deactive'){
                $("#Userstatuscheck_"+user_id).val(2);
                $("#Userstatuscheck_"+user_id).prop('checked',false);
                host_user_page_tabs(tab_type);
                toastr.success("Customer Deactivated",'Success',{timeOut: 5000});
            }
            if(res.status == 200 && res.action=='active'){
                $("#Userstatuscheck_"+user_id).val(1);
                $("#Userstatuscheck_"+user_id).prop('checked',true);
                host_user_page_tabs(tab_type);
                toastr.success("Customer activated",'Success',{timeOut: 5000});
            }
        },
        error: function (data) {
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
}
</script>

<!-- end_user list JS end -->
@endsection

