<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SettingModel;
use App\Models\DepositModel;
use App\Models\WithdrawModel;
use App\Models\ContactModel;

class InfoWebController extends BaseController
{
    protected $setting_model;
    protected $deposit_model;
    protected $withdraw_model;
    protected $contact_model;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->setting_model = new SettingModel();
        $this->deposit_model = new DepositModel();
        $this->withdraw_model = new WithdrawModel();
        $this->contact_model = new ContactModel();
    }

    public function getInfoApp(): array
    {
        $user_model = new UserModel();

        $curr_code = ($this->setting_model)->select('curr_code')->first();

        $setting = $this->db->table('settings')->select(['site_name', 'keywords', 'description'])->get(1)->getFirstRow();

        $deposits_array = ($this->deposit_model)
            ->join('users', 'users.id = deposit_histories.user_id')
            ->where('status', 'paid')
            ->get()
            ->getResult();

        $withdraws_array = ($this->withdraw_model)
            ->join('users', 'users.id = withdraw_histories.user_id')
            ->where('status', 'paid')
            ->get()
            ->getResult();

        $start = ($this->setting_model)->find(1)['start_from'];
        $targetDate = new \DateTime($start, new \DateTimeZone('UTC'));
        $currentDate = new \DateTime('now', new \DateTimeZone('UTC'));
        $interval = $currentDate->diff($targetDate);

        $tot_dp = (object)($this->deposit_model)
            ->selectSum('sum_deposit')
            ->where('status', 'paid')
            ->get()
            ->getRow();

        $tot_wd = (object)($this->withdraw_model)
            ->selectSum('sum_withdraw')
            ->where('status', 'paid')
            ->get()
            ->getRow();

        $email = $this->contact_model->where('sosmed', 'Email')->get()->getRow();
        $telegram = $this->contact_model->where('sosmed', 'Telegram')->get()->getRow();

        $data = [
            'sitename' => $setting->site_name,
            'slogan' => 'Simple earning',
            'description' => $setting->description,
            'keywords' => $setting->keywords,
            'web_stats' => [
                'total_user' => $user_model->countAll() ?: 0,
                'total_deposit' => $tot_dp->sum_deposit ?: 0,
                'total_withdraw' => $tot_wd->sum_withdraw ?: 0,
                'online_day' => $interval->days,
            ],
            'deposits' => $deposits_array,
            'withdraws' => $withdraws_array,
            'crypto' => $curr_code,
            'email' => $email,
            'telegram' => $telegram
        ];

        return $data;
    }
}
