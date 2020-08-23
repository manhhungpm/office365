<?php

namespace App\Console\Commands;

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class OfficeGetTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'office:get-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Office get token';

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
            $url = Str::replaceArray('?', [$account->tenant_id], API_TOKEN);
            $data = sendRequest($url, [
                'client_id' => $account->client_id,
                'scope' => 'https://graph.microsoft.com/.default',
                'client_secret' => $account->client_secret,
                'grant_type' => 'client_credentials'
            ]);

            if ($data != '') {
                $result = json_decode($data);

                $account->access_token = $result->access_token;
                $account->expires_in = $result->expires_in;
                $account->token_at = Carbon::now();

                $account->save();
            }
        }
    }
}
