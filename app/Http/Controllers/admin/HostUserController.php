<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProjectPage;
use App\Models\Agency;
use App\Exports\HostUsersExport;
use Maatwebsite\Excel\Facades\Excel;

class HostUserController extends Controller
{
    private $page = "Users";

    public function host_index()
    {
        $action = "list";
        $users = User::where('role', 5)->get();
        return view('admin.host_users.list',compact('users','action'))->with('page',$this->page);
    }

    public function allHostuserlist(Request $request)
    {
        if ($request->ajax()) {
            $tab_type = $request->tab_type;
            $agency_search = $request->agency_search;
            if ($tab_type == "active_host_user_tab"){
                $estatus = 1;
            }
            elseif ($tab_type == "deactive_host_user_tab"){
                $estatus = 2;
            }

            $columns = array(
                0=>'id',
                1=>'profile_pic',
                2=> 'contact_info',
                3=> 'other_info',
                4=> 'user',
                5=> 'coin',
                6=> 'estatus',
                7=> 'created_at',
                // 7=> 'action',
            );

            $totalData = User::whereIn('role', ['5'])->WhereNotNull('first_name');
            if (isset($estatus)){
                $totalData = $totalData->where('estatus',$estatus);
            }
            if (isset($agency_search)){
                $totalData = $totalData->where('agency_id', $agency_search);
            }
            
            $totalData = $totalData->count();

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
                $users = User::whereIn('role', ['5'])->WhereNotNull('first_name');
                if (isset($estatus)){
                    $users = $users->where('estatus',$estatus);
                }
                if (isset($agency_search)){
                    $users = $users->where('agency_id',$agency_search);
                }
               
                $users = $users->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $users =  User::whereIn('role', ['5'])->WhereNotNull('first_name');
                if (isset($estatus)){
                    $users = $users->where('estatus',$estatus);
                }
                if (isset($agency_search)){
                    $users = $users->where('agency_id',$agency_search);
                }
                
                $users = $users->where(function($query) use($search){
                    $query->where('first_name','LIKE',"%{$search}%")
                        ->orWhere('last_name', 'LIKE',"%{$search}%")
                        ->orWhere('email', 'LIKE',"%{$search}%")
                        ->orWhere('mobile_no', 'LIKE',"%{$search}%")
                        ->orWhere('created_at', 'LIKE',"%{$search}%");
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($users->toArray());
            }

            $data = array();
    
            if(!empty($users))
            {
                foreach ($users as $user)
                {
                    $page_id = ProjectPage::where('route_url','admin.end_users.list')->pluck('id')->first();

                    if( $user->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="HostUserstatuscheck_'. $user->id .'" onchange="changeHostuserstatus('. $user->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($user->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="HostUserstatuscheck_'. $user->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $user->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="HostUserstatuscheck_'. $user->id .'" onchange="changeHostuserstatus('. $user->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($user->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="HostUserstatuscheck_'. $user->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    // if(isset($user->images) && $user->images!=null){
                    //     $images=explode(',',$user->images);
                    //     $profile_pic = url($images[0]);
                    // }
                    // else{
                    //     $profile_pic = url('images/default_avatar.jpg');
                    // }

                    if($user->profile_pic){
                        $profile_pic = $user->profile_pic;
                    }
                    else{
                        $profile_pic = url('images/default_avatar.jpg');
                    }

                    $contact_info = '';
                    if (isset($user->email)){
                        $contact_info .= '<span><i class="fa fa-envelope" aria-hidden="true"></i> ' .$user->email .'</span>';
                    }
                    if (isset($user->mobile_no)){
                        $contact_info .= '<span><i class="fa fa-phone" aria-hidden="true"></i> ' .$user->mobile_no .'</span>';
                    }

                    $other_info = '';
                    if ($user->gender == 1){
                        $other_info = '<span><i class="fa fa-male" aria-hidden="true"  style="font-size: 20px; margin-right: 5px"></i> Male</span>';
                    } else if($user->gender == 2){
                        $other_info = '<span><i class="fa fa-female" aria-hidden="true" style="font-size: 20px; margin-right: 5px"></i> Female</span>';
                    } else {
                        $other_info = '<span><i class="fa fa-male" aria-hidden="true" style="font-size: 20px; margin-right: 5px"></i> Other</span>';
                    }
                    if($user->age != NULL){
                        $other_info .= '<span><i class="fa fa-birthday-cake" aria-hidden="true"></i> '.$user->age.'</span>';
                    }
                    if($user->location != NULL){
                        $other_info .= '<span><i class="fa fa-map-marker" aria-hidden="true"></i> '.$user->location.'</span>';
                    }

                    $full_name = "";
                    if(isset($user->first_name)){
                        $full_name = $user->first_name;
                    }
                    if(isset($user->last_name) && !empty($user->last_name)){
                        $full_name .= ' '.$user->last_name;
                    }

                    $action='';
                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editEndUserBtn" class="btn btn-gray text-blue btn-sm" data-id="' .$user->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteEndUserBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteEndUserModal" onclick="" data-id="' .$user->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['profile_pic'] = '<img src="'. $profile_pic .'" width="50px" height="50px" alt="Profile Pic"><span>'.$full_name.'</span>';
                    $nestedData['contact_info'] = $contact_info;
                    $nestedData['login_info'] = $other_info;
                    $nestedData['user'] = 'Host User';
                    $nestedData['coin'] = $user->coin;
                    $nestedData['g_coin'] = $user->g_coin;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($user->created_at));
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

    public function changeHostuserstatus($id){
        $user = User::find($id);
        if ($user->estatus==1){
            $user->estatus = 2;
            $user->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($user->estatus==2){
            $user->estatus = 1;
            $user->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function export(Request $request)
    {
        $coin_update = isset($request->coin_update) ? 1 : 0;
        $tab_type = $request->tab_type_export;
        $agency_id = $request->agency_search_export;
        $agency_name = 'host_users.xlsx';
        $agency = NULL;
        if($agency_id)
        {
            $agency = Agency::where('id', $agency_id)->first();
            $agency_name = $agency ? $agency->agency_name.'.xlsx' : $agency_name;
        }
        return Excel::download(new HostUsersExport($tab_type, $agency_id, $coin_update, $agency), $agency_name);
    }
}
