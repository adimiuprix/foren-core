<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PlanModel;
use App\Models\UserModel;
use App\Models\BonusModel;
use App\Models\WithdrawModel;

class PanelController extends BaseController
{
    protected $user_model;
    protected $plan_model;
    protected $withdraw_model;
    protected $user_session;

    public function __construct()
    {
        $this->user_model = new UserModel();
        $this->plan_model = new PlanModel();
        $this->withdraw_model = new WithdrawModel();
        $id_user = session()->get('user_data');
        $this->user_session = $this->user_model->where('id', $id_user['id'])->get()->getFirstRow();
    }

    public function account()
    {
        $user_session = session()->get('user_data');
        $this->plan_model->plans_cron($user_session);
        $balance = $this->user_model->getBalance($user_session);
        $this->user_model->updateBalances($user_session, $balance);
        $user_balance = $this->user_model->getUserBalance($user_session);
        $total_balance = floatval($user_balance);

        $active_plans = (array)$this->user_model->getActivePlans($user_session);

        $earning_point = array_reduce($active_plans, function($carry, $plan) {
            return $carry + (float) ($plan['earning_per_day'] ?? 0);
        }, 0);

        $tot_active_deposits = array_sum(array_column($active_plans, 'price'));

        $earn_point = [
            'hourly' => number_format($earning_point / 24, 4),
            'daily' => number_format($earning_point, 4),
            'tot_deposit' => $tot_active_deposits,
        ];

        $user_earning_rate = array_sum(array_column($active_plans, 'earning_rate'));

        $plans = $this->plan_model->where('is_free', 0)->get()->getResultObject();

        $earnbal = $this->user_model->where('id', $user_session['id'])->select('earning_balance')->get()->getRow();

        $data = array_merge([
            'address' => $user_session['username'],
            'balance' => $total_balance,
            'earning_rate' => $user_earning_rate,
            'earning' => $earn_point,
            'plans' => $plans,
            'earning_balance' => $earnbal->earning_balance,
        ], $this->web_data);

        return view('user/account', $data);
    }

    public function bonus()
    {
        $bonus_model = new BonusModel();
        $claims = $bonus_model
            ->join('users', 'users.id = bonuses.user_id')
            ->where('status', 'credited')
            ->orderBy('bonuses.id', 'DESC')
            ->limit(10)
            ->get()
            ->getResult();

        $data = array_merge([
                'claims' => $claims
            ],
            $this->web_data
        );

        return view('user/bonus', $data);
    }

    public function refs()
    {
        $income_ref = $this->user_model->selectSum('upline_reward')->where('reff_by', $this->user_session->id)->get()->getRow();
        $reff_count = $this->user_model->where('reff_by', $this->user_session->id)->countAllResults();
        $referrals = $this->user_model->select(['id', 'username', 'user_wallet', 'total_earn', 'reff_by', 'upline_reward'])->where('reff_by', '1', true)->get()->getResult();
        $reff_link = base_url('ref/?refflink=' . session()->get('user_data')['reff_code']);

        $data = array_merge([
            'income_ref' => number_format($income_ref->upline_reward, 2) ?: 0,
            'ref_count' => $reff_count ?: 0,
            'reff_link' => $reff_link,
            'referrals' => $referrals
        ], $this->web_data);

        return view('user/refs', $data);
    }

    public function deposit()
    {
        return view('user/deposit', $this->web_data);
    }

    public function withdraw()
    {
        $wallet = session()->get('user_data')['user_wallet'];
        $id_user = $this->user_model->where('user_wallet', $wallet)->get()->getRow()->id;
        $withdrawals = $this->withdraw_model->where('user_id', $id_user)->findAll();

        $data = array_merge([
            'wallet' => $wallet,
            'withdrawals' => $withdrawals
        ], $this->web_data);

        return view('user/withdraw', $data);
    }
}
