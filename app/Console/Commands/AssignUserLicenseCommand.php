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

        $configLicense = $this->getConfigLicense();

        if ($account != null) {
            $url = Str::replaceArray('?', [$id], API_ASSIGN_LICENSE);

            sendRequest($url, json_encode($configLicense[0]), $account->access_token, 'POST', true);
        }

    }

    public function getConfigLicense()
    {
        $query = LicenseConfig::query()->get()->toArray();
        $licenseParent = [];

        foreach ($query as $item) {
            array_push($licenseParent, $item['license_parent']);
        }

        $configLicense = [
            "addLicenses" => [
                "disabledPlans" => [],
                "skuId" => null
            ],
            "removeLicenses" => []
        ];
        dd(array_unique($licenseParent));


        foreach (array_unique($licenseParent) as $item) {
            array_push($configLicense[], [
                "addLicenses" => [
                    "disabledPlans" => [],
                    "skuId" => $item
                ],
                "removeLicenses" => []
            ]);
        }

        //tạo license config
        for ($i = 0; $i < sizeof($query); $i++) {
            for ($j = 0; $j < sizeof($configLicense); $j++) {
                if ($query[$i]['license_parent'] == $configLicense[$j]['addLicenses']['skuId']) {
                    $configLicense[$j]['addLicenses']['disabledPlans'][] = $query[$i]['license_child'];
                }
            }
        }

        return ($configLicense);
    }
}
