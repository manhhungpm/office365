<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\LicenseConfig;
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

        $configLicense = $this->getConfigLicense($account->id);

        if ($account != null) {
            $url = Str::replaceArray('?', [$id], API_ASSIGN_LICENSE);

            sendRequest($url, $configLicense, $account->access_token, 'POST', true);
        }

    }

    public function getConfigLicense($id)
    {
        $query = LicenseConfig::query()->where('account_id',$id)->get()->toArray();
        $licenseParent = [];

        foreach ($query as $item) {
            array_push($licenseParent, $item['license_parent']);
        }

        $configLicense = [
            "addLicenses" => [],
            "removeLicenses" => []
        ];

        foreach (array_unique($licenseParent) as $item){
            array_push($configLicense["addLicenses"],[
                "disabledPlans" => [],
                "skuId" => $item
            ]);
        }


        for ($i=0;$i<sizeof($query);$i++){
            for ($j=0;$j<sizeof($configLicense["addLicenses"]);$j++){
                if ($query[$i]["license_parent"] == $configLicense["addLicenses"][$j]["skuId"]){
                    if(!is_null($query[$i]["license_child"])){
                        array_push($configLicense["addLicenses"][$j]["disabledPlans"],$query[$i]["license_child"]);
                    }
                }
            }
        }

        return ($configLicense);
    }
}
