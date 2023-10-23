@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Agency History</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-pane fade show active table-responsive" id="agency_tab">
                            <table id="agency_page_table" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Agency Name</th>
                                    <th>Coin</th>
                                    <th>G Coin</th>
                                    <th>Status</th>
                                    <th>Excel Sheet</th>
                                    <th>Remark</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Agency Name</th>
                                    <th>Coin</th>
                                    <th>G Coin</th>
                                    <th>Status</th>
                                    <th>Excel Sheet</th>
                                    <th>Remark</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="agencyModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="agencyform" method="post" enctype="multipart/form-data" class="form-valide">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formtitle">Agency Details</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-form-label" for="title" id="label_title">Sheet <span class="text-danger">*</span>
                            </label>
                            <input type="checkbox" name="coin_status" id="coin_status" value="1" style="float: right;">
                            <input type="file" class="form-control input-flat" id="sheet" name="sheet" placeholder="">
                            <div id="sheet-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="title" id="label_title">Remark <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="remark" name="remark" placeholder="">
                            <div id="remark-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="agency_history_id" id="agency_history_id">
                        
                        <button type="button" class="btn btn-primary" id="save_closeAgencyBtn">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- agency JS start -->
    <script type="text/javascript">
      
      
        function save_agency(btn,btn_type){
            $(btn).prop('disabled',true);
            $(btn).find('.loadericonfa').show();
            var formData = new FormData($("#agencyform")[0]);

            $.ajax({
                type: 'POST',
                url: "{{ url('admin/addorupdateagencyhistory') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if(res.status == 'failed'){
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled',false);
                        if (res.errors.sheet) {
                            $('#sheet-error').show().text(res.errors.sheet);
                        } else {
                            $('#sheet-error').hide();
                        }
                    }

                    if(res.status == 200){
                        if(btn_type == 'save_close'){
                            $("#agencyModal").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled',false);
                            if(res.action == 'add'){
                                agency_page_tabs();
                                toastr.success("Agency Sheet Added",'Success',{timeOut: 5000});
                            }
                            if(res.action == 'update'){
                                agency_page_tabs();
                                toastr.success("Agency Sheet Updated",'Success',{timeOut: 5000});
                            }
                        }

                        if(btn_type == 'save_new'){
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled',false);
                            $("#agencyModal").find('form').trigger('reset');
                            $('#agency_history_id').val("");
                            $('#sheet-error').html("");
                            $("#agencyModal").find("#save_closeAgencyBtn").removeAttr('data-action');
                            $("#agencyModal").find("#save_closeAgencyBtn").removeAttr('data-id');
                            $("#remark").focus();
                            if(res.action == 'add'){
                                agency_page_tabs();
                                toastr.success("Agency Sheet Added",'Success',{timeOut: 5000});
                            }
                            if(res.action == 'update'){
                                agency_page_tabs();
                                toastr.success("Agency Sheet Updated",'Success',{timeOut: 5000});
                            }
                        }
                    }

                    if(res.status == 400){
                        $("#agencyModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled',false);
                       
                        toastr.error("Please try again",'Error',{timeOut: 5000});
                    }
                },
                error: function (data) {
                    $("#agencyModal").modal('hide');
                    $(btn).find('.loadericonfa').hide();
                    $(btn).prop('disabled',false);
            
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            });
        }

        $('body').on('click', '#save_closeAgencyBtn', function () {
            save_agency($(this),'save_close');
        });

        $('#agencyModal').on('shown.bs.modal', function (e) {
            $("#title").focus();
        });

        $('#agencyModal').on('hidden.bs.modal', function () {
            $(this).find('form').trigger('reset');
            $(this).find("#save_closeAgencyBtn").removeAttr('data-action');
            $(this).find("#save_closeAgencyBtn").removeAttr('data-id');
            $('#agency_history_id').val("");
            $('#sheet-error').html("");
        });

        $(document).ready(function() {
            agency_page_tabs('',true);
        });

        function agency_page_tabs(tab_type='',is_clearState=false) {
            if(is_clearState){
                $('#agency_page_table').DataTable().state.clear();
            }

            $('#agency_page_table').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                'stateSave': function(){
                    if(is_clearState){
                        return false;
                    }
                    else{
                        return true;
                    }
                },
                "ajax":{
                    "url": "{{ url('admin/allAgencyHistorylist') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type},
                    // "dataSrc": ""
                },
                'columnDefs': [
                    { "width": "50px", "targets": 0 },
                    { "width": "145px", "targets": 1 },
                    { "width": "165px", "targets": 2 },
                    { "width": "120px", "targets": 3 },
                    { "width": "115px", "targets": 4 },
                ],
                "columns": [
                    {data: 'id', name: 'id', class: "text-center" , orderable: false ,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {data: 'agency_name', name: 'agency_name', class: "text-left"},
                    {data: 'coin', name: 'coin', orderable: false, searchable: false, class: "text-center"},
                    {data: 'g_coin', name: 'g_coin', searchable: false, class: "text-left"},
                    {data: 'status', name: 'status', searchable: false, class: "text-left"},
                    {data: 'excel_sheet', name: 'excel_sheet', searchable: false, class: "text-left"},
                    {data: 'remark', name: 'remark', searchable: false, class: "text-left"},
                    {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
                    {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
                ]
            });
        }

        $('body').on('click', '#editAgencyBtn', function () {
            var edit_agency_history_id = $(this).attr('data-id');

            $.get("{{ url('admin/agency-history') }}" +'/' + edit_agency_history_id +'/edit', function (data) {
                $('#agencyModal').find('.modal-title').html("Agency Details");
                $('#agencyModal').find('#save_closeAgencyBtn').attr("data-action","update");
                $('#agencyModal').find('#save_closeAgencyBtn').attr("data-id",edit_agency_history_id);
                $('#agency_history_id').val(data.id);
                if(data.status) {
                    $('#coin_status').prop('checked', true);
                }
                $('#remark').val(data.remark);
            });
        });

    </script>
    <!-- attribute JS end-->
@endsection

