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
        return ['Id', 'First Name', 'Last Name', 'Email', 'Agency Name', 'Coin', 'G Coin', 'Total Coin', 'Amount'];
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

        $users = User::select('id', 'first_name', 'last_name', 'email', 'coin', 'g_coin', 'agency_id as agency_name', 'estatus')->whereHas('agency')->whereIn('role', ['5'])->WhereNotNull('first_name');
        if (isset($estatus)){
            $users = $users->where('estatus', $estatus);
        }
        if (isset($agency_id)){
            $users = $users->where('agency_id', $agency_id);
        }
        $usersData = $users;
        $usersData = $usersData->get();

        $hostUser = [];

        $agency_total_coin = 0;
        $agency_total_g_coin = 0;
        $sum_of_coin = 0;
        $sum_of_amount = 0;

        foreach($usersData as $key => $user)
        {
            if($user->estatus == 2)
            {
                $total_coin = 0;
                $amount = 0;
            }
            else
            {
                $total_coin = $user->coin + $user->g_coin;
    
                $amount = $total_coin;
                if($agency) {
                    $amount = ($total_coin/100) * $agency->count;
                }
            }

            $agency_name = $agencyData[$user->agency_name];

            $user_data['id'] = $user->id;
            $user_data['first_name'] = $user->first_name;
            $user_data['last_name'] = $user->last_name;
            $user_data['email'] = $user->email;
            $user_data['agency_name'] = $agency_name;
            $user_data['coin'] = $user->coin ?? '0';
            $user_data['g_coin'] = $user->g_coin ?? '0';
            $user_data['total_coin'] = $total_coin ?? '0';
            $user_data['amount'] = $amount ?? '0';
            $hostUser[] = $user_data;

            // coin
            $agency_total_coin += $user->coin;
            $agency_total_g_coin += $user->g_coin;
            $sum_of_coin += $total_coin;
            $sum_of_amount += $amount;
            
            if($coin_update && isset($agency_id) && $agency_id != 0) {
                UserCoinHistory::create([
                    'agency_id' => $agency_id,
                    'user_id' => $user->id,
                    'coin' => $user->coin,
                    'g_coin' => $user->g_coin,
                ]);
            }
        }

        $user_data['id'] = '';
        $user_data['first_name'] = '';
        $user_data['last_name'] = '';
        $user_data['email'] = '';
        $user_data['agency_name'] = '';
        $user_data['coin'] = '';
        $user_data['g_coin'] = 'Total';
        $user_data['total_coin'] = $sum_of_coin ?? '0';
        $amount_1 = $sum_of_amount ?? '0';
        $user_data['amount'] = $amount_1;
        $hostUser[] = $user_data;

        $user_data['id'] = '';
        $user_data['first_name'] = '';
        $user_data['last_name'] = '';
        $user_data['email'] = '';
        $user_data['agency_name'] = '';
        $user_data['coin'] = '';
        $user_data['g_coin'] = 'Minute';
        $user_data['total_coin'] = $sum_of_coin ? $sum_of_coin/100 : '0';
        $user_data['amount'] = '';
        $hostUser[] = $user_data;

        $user_data['id'] = '';
        $user_data['first_name'] = '';
        $user_data['last_name'] = '';
        $user_data['email'] = '';
        $user_data['agency_name'] = '';
        $user_data['coin'] = '';
        $user_data['g_coin'] = 'Commission';
        $user_data['total_coin'] = '';
        $amount_2 = $sum_of_amount ? ($sum_of_amount * 15) / 100 : '0';
        $user_data['amount'] = $amount_2;
        $hostUser[] = $user_data;

        $user_data['id'] = '';
        $user_data['first_name'] = '';
        $user_data['last_name'] = '';
        $user_data['email'] = '';
        $user_data['agency_name'] = '';
        $user_data['coin'] = '';
        $user_data['g_coin'] = 'Final Amount';
        $user_data['total_coin'] = '';
        $user_data['amount'] = $amount_1 + $amount_2;
        $hostUser[] = $user_data;

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

        $hostUser = collect($hostUser);
        return $hostUser;
    }
}
