<?php
namespace App\Repositories\Common;

use App\Repositories\BaseRepository;
use App\Models\ActionsLog;
use Illuminate\Support\Facades\DB;

class LogsReponsitory extends BaseRepository
{
    public function model()
    {
        return ActionsLog::class;
    }

    public function getList($keyword = null, $search = [], $counting = false, $limit = 10, $offset = 0, $orderBy = 'created_at', $orderType = 'desc')
    {

        $query = $this->model
            ->where('username', 'LIKE', "%$keyword%");

        collect($search)->each(function ($item, $key) use ($query) {
            switch ($key) {
                case 'timeFrom':
                    $query->whereDate('created_at', '>=', $item);
                    break;
                case 'timeTo':
                    $query->whereDate('created_at', '<=', $item);
                    break;
                case 'action_name':
                    if ($item != 'All') {
                        $query->where($key, 'LIKE', "%$item%");
                    }
                    break;
                case 'class_name':
                    if ($item != 'All') {
                        $query->where($key, 'LIKE', "%$item%");
                    }
                    break;
                case 'username':
                    if ($item != 'Tất cả') {
                        $query->where($key, "$item");
                    }
                    break;
                default:
                    $query->where($key, 'LIKE', "%$item%");
                    break;
            }
        });

        if (!$counting) {
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
}