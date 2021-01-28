<?php

namespace App\Repositories;

use App\Models\Account;
use App\Models\LicenseConfig;

class LicenseConfigRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return LicenseConfig::class;
    }

    public function getList($keyword = null,$searchParams = null, $counting = false, $limit = 10, $offset = 0, $orderBy = 'name', $orderType = 'asc')
    {
        $id = $searchParams;
        $query = $this->model->where('account_id',$id);

        if (!$counting) {
            $query->select('*');
            if ($limit > 0) {
                $query->skip($offset)
                    ->take($limit);
            }

            if ($orderBy != null && $orderType != null) {
                $query->orderBy($orderBy, $orderType);
            }

        } else {
            return $query->count();
        }

        return $query->get();
    }

    public function add($arr)
    {
        $arrLicense = $arr['assigned_licenses'];
        $id = $arr['account_id'];

        //Xóa các cấu hình cũ đi
        LicenseConfig::where('account_id',$id)->delete();

        foreach ($arrLicense as $value) {
            if (sizeof($value['disabledPlans']) != 0) { //Có license con
                foreach ($value['disabledPlans'] as $child) {
                    $query = new LicenseConfig();
                    $query->license_parent = $value['skuId'];
                    $query->license_child = $child['id'];
                    $query->parent_name = $value['name'];
                    $query->child_name = $child['name'];
                    $query->account_id = $id;
                    $query->save();
                }
            } else { //Ko có license con
                $query = new LicenseConfig();
                $query->license_parent = $value['skuId'];
                $query->parent_name = $value['name'];
                $query->account_id = $id;
                $query->save();
            }

        }

        return true;
    }
}
