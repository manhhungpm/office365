<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Domain;
use App\Models\MSUser;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestSyncUser extends Command
{
    protected $signature = 'test:sync';

    protected $description = 'Sync user for Office 365';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
//        $this->info('The command was successful!');
//        dd(123);
        $token = 'eyJ0eXAiOiJKV1QiLCJub25jZSI6IlRGR1pGelYwYWI3bXl1X3BlZUlQVFlDZ0FfQVA0MUdqeFBjcjlrLS1rbWsiLCJhbGciOiJSUzI1NiIsIng1dCI6Im5PbzNaRHJPRFhFSzFqS1doWHNsSFJfS1hFZyIsImtpZCI6Im5PbzNaRHJPRFhFSzFqS1doWHNsSFJfS1hFZyJ9.eyJhdWQiOiJodHRwczovL2dyYXBoLm1pY3Jvc29mdC5jb20iLCJpc3MiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC85OThjNTc5Zi05NmRiLTQxMmYtODU5Yy1mMTU5NGQzM2Q0YzEvIiwiaWF0IjoxNjIxNDM0MzA3LCJuYmYiOjE2MjE0MzQzMDcsImV4cCI6MTYyMTQzODIwNywiYWlvIjoiRTJaZ1lJaE05MkxyZUQ1MVNwR1k3RFltMzNXckFBPT0iLCJhcHBfZGlzcGxheW5hbWUiOiIzNjVNYW5hZ2VyIiwiYXBwaWQiOiJjZTM2OGVlYy1kMWZlLTQ0MzEtYjYwYi1mN2E4YjU5ZmZiNDUiLCJhcHBpZGFjciI6IjEiLCJpZHAiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC85OThjNTc5Zi05NmRiLTQxMmYtODU5Yy1mMTU5NGQzM2Q0YzEvIiwiaWR0eXAiOiJhcHAiLCJvaWQiOiJiNzI1N2UwNC1hMWNhLTRkZDMtYTFmNy0yN2I5ZGJkMDkxNjYiLCJyaCI6IjAuQVNzQW4xZU1tZHVXTDBHRm5QRlpUVFBVd2V5T05zNy0wVEZFdGd2M3FMV2YtMFVyQUFBLiIsInJvbGVzIjpbIlVzZXIuUmVhZFdyaXRlLkFsbCIsIkRvbWFpbi5SZWFkV3JpdGUuQWxsIiwiRGlyZWN0b3J5LlJlYWRXcml0ZS5BbGwiLCJVc2VyLk1hbmFnZUlkZW50aXRpZXMuQWxsIl0sInN1YiI6ImI3MjU3ZTA0LWExY2EtNGRkMy1hMWY3LTI3YjlkYmQwOTE2NiIsInRlbmFudF9yZWdpb25fc2NvcGUiOiJBUyIsInRpZCI6Ijk5OGM1NzlmLTk2ZGItNDEyZi04NTljLWYxNTk0ZDMzZDRjMSIsInV0aSI6InpyOXBkZ2hXTFVtZmJVQV9Vd0loQUEiLCJ2ZXIiOiIxLjAiLCJ4bXNfdGNkdCI6MTUzMjUyOTY4Nn0.KUlAIqT76vlnfXB5VWkwTIUYaw5JNIYKEisE7_RjVuHDqSCR1kXDAENq_qbQYxPDsYeU9BDo-Yo0zooDbW5PMhiVXp-hWsSZw7WuU66w9wbyP-UXn8uTeZVFoYQ6kilY4SgtmUWzQpGvbBn_KmjlGkzzrSpd81IDK__nN8gX_Ki-E01Z8mC1dVDRtpnke0yMqldd5FzDv--0iWeZkiAFj8GXtRKCEIF4M-bY0UkJKY7CuShaNLvQgpQzTksT4oQNG3CcoLgbvh_DAcBs8UMFhyB50YGQR_a2GFjUXQvgaPwUyu1AwfJscV_e1xlKyjr1BdNG6o_SfBLZRbUSjtFKCw';
        $url = 'https://graph.microsoft.com/v1.0/users?$top=999&$select=displayName%2cgivenName%2cuserPrincipalName%2cassignedLicenses%2csurname%2caccountEnabled&$skiptoken=X%2744537074020001000000173A7472616E746B696D7468616E684033363565332E636F29557365725F61353062396230642D366635312D346235332D623165372D313836313232623338663237B900000000000000000000%27';
//        $data = sendRequest($url,
//            [], $token, 'GET');
//        $result = json_decode($data);
//        $nextL = $result->{'@odata.nextLink'};
//        dd($nextL);


        $arr = [1,2,3];
        $arr = array_merge($arr , [4,5]);

        dd($arr);


//        $accounts = Account::where('status', ACCOUNT_STATUS_ACTIVE)->get();
//        $domainMap = $this->createDomainMap();
//        foreach ($accounts as $account) {
//            $data = sendRequest(API_USER . '?$top=999&$select=displayName,givenName,mail,passwordProfile,mobilePhone,surname,userPrincipalName,createdDateTime,id,state,userType,accountEnabled', [], $account->access_token, 'GET');
//
//            if ($data != '') {
//                $result = json_decode($data);
//                $users = $result->value;
//
//                foreach ($users as $user) {
//                    $userPrincipalName = $user->userPrincipalName;
//
//                    $arr = explode('@', $userPrincipalName);
//
//
//                    $domainId = isset($domainMap[$arr[1]]) ? $domainMap[$arr[1]] : null;
//
//                    MSUser::updateOrCreate(
//                        ['account_id' => $account->id, 'id' => $user->id],
//                        [
//                            'displayName' => $user->displayName,
//                            'givenName' => $user->givenName,
//                            'mail' => $user->mail,
//                            'passwordProfile' => json_encode($user->passwordProfile),
//                            'mobilePhone' => $user->mobilePhone,
//                            'surname' => $user->surname,
//                            'userPrincipalName' => $user->userPrincipalName,
//                            'createdDateTime' => Carbon::parse($user->createdDateTime),
//                            'id' => $user->id,
//                            'userType' => $user->userType,
//                            'state' => $user->state,
//                            'accountEnabled' => $user->accountEnabled,
//                            'sync_at' => Carbon::now(),
//                            'account_id' => $account->id,
//                            'domain_id' => $domainId
//                        ]
//                    );
//                }
//
//                //Xóa những thằng có sync_at nhỏ hơn carbon->now - 1ph
//                //tại những thằng có trên server được đồng bộ thì sẽ được đồng bộ lại sync_at (5ph 1 lần)
//                //còn nếu không có thì sync at vẫn giữ nguyên
//
//                $msUserDelete = MSUser::select('ms_user_id','sync_at')
//                    ->where('account_id',$account->id)
//                    ->where('sync_at', '<', Carbon::now()->subMinute())
//                    ->get()->toArray();
//                $arrId = [];
//                foreach ($msUserDelete as $item) {
//                    array_push($arrId, $item['ms_user_id']);
//                }
//                MSUser::destroy($arrId);
//            }
//        }
    }
}
