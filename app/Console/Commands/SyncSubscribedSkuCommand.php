<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Domain;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncSubscribedSkuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'office:sync-subscribed-sku {account_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync subscribed sku';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $accountId = $this->argument('account_id');

        $query = Account::where('status', ACCOUNT_STATUS_ACTIVE);
        if ($accountId != null) {
            $query->where('id', $accountId);
        }

        $accounts = $query->get();

        foreach ($accounts as $account) {
            $data = sendRequest(API_SUBSCRIBED_SKU, [], $account->access_token, 'GET');

            if ($data != '') {
                $result = json_decode($data);
                $skus = $result->value;

                if (count($skus) > 0) {
                    $account->skuId = $skus[0]->skuId;
                    $account->save();
                }
            }
        }
    }
}
