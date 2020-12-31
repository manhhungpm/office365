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
        $query = $this->model;

        //Xóa các cấu hình cũ đi
        LicenseConfig::truncate();

        $arrLicense = $arr['assigned_licenses'];
        foreach ($arrLicense as $value){
//            dd($value); //$value->skuId //id thang cha

            if(sizeof($value['disabledPlans']) != 0){ //Có license con
                foreach ($value['disabledPlans'] as $child){
//                dd($child); //id thằng con
                    $query->license_parent = $value['skuId'];
                    $query->license_child = $child;
                    $query->save();
                }
            } else { //Ko có license con
                $query->license_parent = $value['skuId'];
                $query->save();
            }

        }

        return true;
    }
}
