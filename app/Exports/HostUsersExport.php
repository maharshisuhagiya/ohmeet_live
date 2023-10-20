<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Agency;

class HostUsersExport implements FromCollection, withHeadings
{
    private $tab_type, $agency_id, $coin_update;

    public function __construct($tab_type, $agency_id, $coin_update)
    {
        $this->tab_type = $tab_type;
        $this->agency_id = $agency_id;
        $this->coin_update = $coin_update;
    }

    public function headings(): array
    {
        return ['Id', 'First Name', 'Last Name', 'Email', 'Mobile No', 'Coin', 'Agency Name'];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $agency = Agency::pluck('agency_name', 'id')->toArray();
        $tab_type = $this->tab_type;
        $agency_id = $this->agency_id;
        $coin_update = $this->coin_update;

        if ($tab_type == "active_host_user_tab"){
            $estatus = 1;
        }
        elseif ($tab_type == "deactive_host_user_tab"){
            $estatus = 2;
        }

        $users = User::select('id', 'first_name', 'last_name', 'email', 'mobile_no', 'coin', 'g_coin', 'agency_id as agency_name')->whereHas('agency')->whereIn('role', ['5'])->WhereNotNull('first_name');
        if (isset($estatus)){
            $users = $users->where('estatus', $estatus);
        }
        if (isset($agency_id)){
            $users = $users->where('agency_id', $agency_id);
        }
        $usersData = $users;
        $usersData = $usersData->get();

        foreach($usersData as $key => $user)
        {
            $agency_name = $agency[$user->agency_name];
            unset($usersData[$key]['agency_name']);
            $usersData[$key]['agency_name'] = $agency_name;
            $usersData[$key]['coin'] = (int)$user->coin + (int)$user->g_coin;
            unset($usersData[$key]['g_coin']);
        }

        if($coin_update)
        {
            $users->update(['coin' => 0, 'g_coin' => 0]);
        }

        return $usersData;
    }
}
