<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\MSUser;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AssignUserLicenseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'office:assign-user-license {accountId} {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign user license';

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
        $accountId = $this->argument('accountId');
        $id = $this->argument('id');

        $account = Account::find($accountId);

        if($account != null){
            $url = Str::replaceArray('?', [$id], API_ASSIGN_LICENSE);

            sendRequest($url, [
                'addLicenses' => [
                    [
                        'skuId' => $account->skuId
                    ]
                ],
                'removeLicenses' => []
            ], $account->access_token, 'POST', true);
        }

    }
}
