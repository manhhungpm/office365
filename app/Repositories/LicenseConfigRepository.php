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

    public function getList($keyword = null, $counting = false, $limit = 10, $offset = 0, $orderBy = 'name', $orderType = 'asc')
    {
        $query = $this->model;

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
        //Xóa các cấu hình cũ đi
        LicenseConfig::truncate();

        $arrLicense = $arr['assigned_licenses'];

        foreach ($arrLicense as $value) {
            if (sizeof($value['disabledPlans']) != 0) { //Có license con
                foreach ($value['disabledPlans'] as $child) {
                    $query = new LicenseConfig();
                    $query->license_parent = $value['skuId'];
                    $query->license_child = $child['id'];
                    $query->parent_name = $value['name'];
                    $query->child_name = $child['name'];
                    $query->save();
                }
            } else { //Ko có license con
                $query = new LicenseConfig();
                $query->license_parent = $value['skuId'];
                $query->parent_name = $value['name'];
                $query->save();
            }

        }

        return true;
    }
}
