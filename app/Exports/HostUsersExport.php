<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Agency;
use App\Models\AgencyCoinHistory;
use App\Models\UserCoinHistory;

class HostUsersExport implements FromCollection, withHeadings
{
    private $tab_type, $agency_id, $coin_update, $agency;

    public function __construct($tab_type, $agency_id, $coin_update, $agency)
    {
        $this->tab_type = $tab_type;
        $this->agency_id = $agency_id;
        $this->coin_update = $coin_update;
        $this->agency = $agency;
    }

    public function headings(): array
    {
        return ['Id', 'First Name', 'Last Name', 'Email', 'Mobile No', 'Coin', 'G Coin', 'Agency Name', 'Total Coin', 'Amount'];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $agencyData = Agency::pluck('agency_name', 'id')->toArray();
        $tab_type = $this->tab_type;
        $agency_id = $this->agency_id;
        $coin_update = $this->coin_update;
        $agency = $this->agency;

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

        $agency_total_coin = 0;
        $agency_total_g_coin = 0;
        foreach($usersData as $key => $user)
        {
            $agency_name = $agencyData[$user->agency_name];
            unset($usersData[$key]['agency_name']);
            $usersData[$key]['agency_name'] = $agency_name;

            $total_coin = (int)$user->coin + (int)$user->g_coin;
            $usersData[$key]['total_coin'] = $total_coin;

            $usersData[$key]['amount'] = $total_coin;
            if($agency) {
                $usersData[$key]['amount'] = ($total_coin/100) * $agency->count;
            }

            // coin
            $agency_total_coin += (int)$user->coin;
            $agency_total_g_coin += (int)$user->g_coin;
            
            if($coin_update && isset($agency_id) && $agency_id != 0) {
                UserCoinHistory::create([
                    'agency_id' => $agency_id,
                    'user_id' => $user->id,
                    'coin' => $user->coin,
                    'g_coin' => $user->g_coin,
                ]);
            }
        }

        if($coin_update)
        {
            if (isset($agency_id)) {
                $AgencyCoinHistory = AgencyCoinHistory::create([
                    'agency_id' => $agency_id,
                    'coin' => $agency_total_coin,
                    'g_coin' => $agency_total_g_coin,
                ]);
            }

            $users->update(['coin' => 0, 'g_coin' => 0]);
        }

        return $usersData;
    }
}
