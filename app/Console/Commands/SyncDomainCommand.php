<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Domain;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncDomainCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'office:sync-domain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync domain for Office 365';

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
        $accounts = Account::where('status', ACCOUNT_STATUS_ACTIVE)->get();

        foreach ($accounts as $account) {
            $data = sendRequest(API_DOMAIN, [], $account->access_token, 'GET');

            if ($data != '') {
                $result = json_decode($data);
                $domains = $result->value;

                foreach ($domains as $domain) {
                    Domain::updateOrCreate(
                        ['account_id' => $account->id, 'id' => $domain->id],
                        [
                            'authenticationType' => $domain->authenticationType,
                            'availabilityStatus' => $domain->availabilityStatus,
                            'isAdminManaged' => $domain->isAdminManaged,
                            'isDefault' => $domain->isDefault,
                            'isInitial' => $domain->isInitial,
                            'isRoot' => $domain->isRoot,
                            'isVerified' => $domain->isVerified,
                            'supportedServices' => json_encode($domain->supportedServices),
                            'state' => $domain->state,
                            'sync_at' => Carbon::now(),
                            'account_id' => $account->id
                        ]
                    );
                }
            }
        }
    }
}
