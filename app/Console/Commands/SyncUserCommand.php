<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Domain;
use App\Models\MSUser;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'office:sync-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync user for Office 365';

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
        $domainMap = $this->createDomainMap();
        foreach ($accounts as $account) {
            $data = sendRequest(API_USER . '?$select=displayName,givenName,mail,passwordProfile,mobilePhone,surname,userPrincipalName,createdDateTime,id,state,userType,accountEnabled', [], $account->access_token, 'GET');

            if ($data != '') {
                $result = json_decode($data);
                $users = $result->value;

                foreach ($users as $user) {
                    $userPrincipalName = $user->userPrincipalName;

                    $arr = explode('@', $userPrincipalName);


                    $domainId = isset($domainMap[$arr[1]]) ? $domainMap[$arr[1]] : null;

                    MSUser::updateOrCreate(
                        ['account_id' => $account->id, 'id' => $user->id],
                        [
                            'displayName' => $user->displayName,
                            'givenName' => $user->givenName,
                            'mail' => $user->mail,
                            'passwordProfile' => json_encode($user->passwordProfile),
                            'mobilePhone' => $user->mobilePhone,
                            'surname' => $user->surname,
                            'userPrincipalName' => $user->userPrincipalName,
                            'createdDateTime' => Carbon::parse($user->createdDateTime),
                            'id' => $user->id,
                            'userType' => $user->userType,
                            'state' => $user->state,
                            'accountEnabled' => $user->accountEnabled,
                            'sync_at' => Carbon::now(),
                            'account_id' => $account->id,
                            'domain_id' => $domainId
                        ]
                    );
                }
            }
        }

        //Xóa những thằng có sync_at nhỏ hơn carbon->now - 1ph
        //tại những thằng có trên server được đồng bộ thì sẽ được đồng bộ lại sync_at (5ph 1 lần)
        //còn nếu không có thì sync at vẫn giữ nguyên

        $msUserDelete = MSUser::select('ms_user_id')->where('sync_at', '<', Carbon::now()->subMinute())->get()->toArray();
        $arrId = [];
        foreach ($msUserDelete as $item) {
            array_push($arrId, $item['ms_user_id']);
        }
        MSUser::destroy($arrId);
    }

    private function createDomainMap()
    {
        return Domain::select('id', 'domain_id')->get()->mapWithKeys(function ($domain) {
            return [$domain->id => $domain->domain_id];
        });
    }
}
