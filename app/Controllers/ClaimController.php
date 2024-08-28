<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BonusModel;

class ClaimController extends BaseController
{
    public function claimRun()
    {
        $user_session = session()->get('user_data');

        $last_claim = (int)$this->user_model->getUserByWallet($user_session['user_wallet'])['last_claim'];

        $can_claim = $last_claim + 86400;

        $current_time = time();

        if ($current_time > $can_claim) {

            // Menampilkan data user
            $user = $this->db->table('users')->where('id', 1)->get()->getFirstRow('object');

            // Menambahkan ke daftar latest bosnus claim
            $bonus_model = new BonusModel();
            $bonus = [
                'user_id' => $user_session['id'],
                'amount_bonus' => 0.05,
                'status' => 'credited',
            ];
            $bonus_model->save($bonus);

            // Memperbarui saldo user
            $newBalance = $user->balance + 0.05;

            $this->user_model->update($user_session['id'], [
                'balance' => $newBalance,
                'last_claim' => time(),
            ]);

            session()->setFlashdata('alert', 'claim_success');
            return redirect()->to('bonus');
        }else{
            session()->setFlashdata('alert', 'claim_failed');
            return redirect()->to('bonus');
        }
    }
}