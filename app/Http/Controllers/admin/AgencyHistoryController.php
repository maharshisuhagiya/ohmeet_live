<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgencyCoinHistory;
use App\Models\ProjectPage;
use Illuminate\Support\Facades\Validator;

class AgencyHistoryController extends Controller
{
    private $page = "Agency History";

    public function index(){
        return view('admin.agency_history.list')->with('page',$this->page);
    }

    public function allAgencyHistorylist(Request $request){
        if ($request->ajax()) {
            
            $columns = array(
                0 =>'id',
                1 =>'agency_name',
                2 => 'coin',
                3 => 'g_coin',
                4 => 'created_at',
                5 => 'action',
            );

            $totalData = AgencyCoinHistory::count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')))
            {
                $agencyData = AgencyCoinHistory::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $agencyData = AgencyCoinHistory::where(function($query) use($search){
                        $query->where('id','LIKE',"%{$search}%")
                                ->orWhere('coin', 'LIKE',"%{$search}%");
                        })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = AgencyCoinHistory::where(function($query) use($search){
                        $query->where('id','LIKE',"%{$search}%")
                        ->orWhere('coin', 'LIKE',"%{$search}%");
                    })
                    ->count();
            }

            $data = array();

            if(!empty($agencyData))
            {
                $page_id = ProjectPage::where('route_url','admin.agency-history.list')->pluck('id')->first();
                foreach ($agencyData as $agency)
                {
                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editAgencyBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#agencyModal" onclick="" data-id="' .$agency->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    
                    $agency_name = $agency->agency ? $agency->agency->agency_name : '';

                    $excel_sheet = '';
                    if($agency->excel_sheet)
                    {
                        $excel_sheet = '<a href="'. asset('public/excel_sheet/'.$agency->excel_sheet) .'" class="btn btn-gray text-blue btn-sm" download="'. $agency_name .'.xlsx">'. $agency_name .'</a>';
                    }

                    $nestedData['agency_name'] = $agency_name;
                    $nestedData['coin'] = $agency->coin;
                    $nestedData['g_coin'] = $agency->g_coin;
                    $nestedData['status'] = $agency->status == 1 ? "Yes" : "No";
                    $nestedData['excel_sheet'] = $excel_sheet;
                    $nestedData['remark'] = $agency->remark;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($agency->created_at));
                    $nestedData['action'] = $action;
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);
        }
    }

    public function editAgencyHistory($id){
        $agencyCoinHistory = AgencyCoinHistory::find($id);
        return response()->json($agencyCoinHistory);
    }

    public function addorupdateagencyhistory(Request $request){

        $agencyCoinHistory = AgencyCoinHistory::find($request->agency_history_id);
        
        if($agencyCoinHistory)
        {
            $old_excel_sheet = $agencyCoinHistory->excel_sheet;
            if(!$old_excel_sheet)
            {
                $messages = [
                    'sheet.required' =>'Please provide a sheet',
                ];
        
                $validator = Validator::make($request->all(), [
                    'sheet' => 'required',
                ], $messages);
        
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
                }
            }

            $agencyCoinHistory->remark = $request->remark;
            $agencyCoinHistory->status = isset($request->coin_status) ? 1 : 0;

            if ($request->hasFile('sheet')) {
                $excel_sheet = $request->file('sheet');
                $excel_sheet_name = 'sheet_' . rand(111111, 999999) . time() . '.' . $excel_sheet->getClientOriginalExtension();
                $destinationPath = public_path('excel_sheet');
                $excel_sheet->move($destinationPath, $excel_sheet_name);
                if(isset($old_excel_sheet)) {
                    $old_excel_sheet = public_path('excel_sheet/' . $old_excel_sheet);
                    if (file_exists($old_excel_sheet)) {
                        unlink($old_excel_sheet);
                    }
                }
                $agencyCoinHistory->excel_sheet = $excel_sheet_name;
            }
            
            $agencyCoinHistory->save();
            return response()->json(['status' => '200', 'action' => 'update']);
        }
        return response()->json(['status' => '400']);
    }
}
