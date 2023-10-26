<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseCoin;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $todayPurchaseCoin = PurchaseCoin::whereDate('created_at', date('Y-m-d'))->count();
        $yesterdayPurchaseCoin = PurchaseCoin::whereDate('created_at', date('Y-m-d', strtotime("-1 days")))->count();

        $todayAmount = PurchaseCoin::whereDate('created_at', date('Y-m-d'))->sum('total_amount');
        $yesterdayAmount = PurchaseCoin::whereDate('created_at', date('Y-m-d', strtotime("-1 days")))->sum('total_amount');

        $todayUser = User::whereDate('created_at', date('Y-m-d'))->count();
        $yesterdayUser = User::whereDate('created_at', date('Y-m-d', strtotime("-1 days")))->count();

        return view('admin.dashboard', compact('todayPurchaseCoin', 'yesterdayPurchaseCoin', 'todayAmount', 'yesterdayAmount', 'todayUser', 'yesterdayUser'));
    }

    public function dateWiseSearch(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');
        $data['todayPurchaseCoin'] = PurchaseCoin::whereDate('created_at', $date)->count();
        $data['todayAmount'] = PurchaseCoin::whereDate('created_at', $date)->sum('total_amount');
        $data['todayUser'] = User::whereDate('created_at', $date)->count();
        return response()->json(['status' => '200', 'data' => $data]);
    }
}
