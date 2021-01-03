<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Account::class;
    }

    /**
     * @param null $keyword
     * @param bool $counting
     * @param int $limit
     * @param int $offset
     * @param string $orderBy
     * @param string $orderType
     * @return mixed
     */
    public function getList($keyword = null, $counting = false, $limit = 10, $offset = 0, $orderBy = 'app_name', $orderType = 'asc')
    {
        $query = $this->model
            ->where('app_name', 'LIKE', "%$keyword%");

        if (!$counting) {
            $query->select('id', 'app_name', 'description', 'client_id', 'client_secret', 'tenant_id', 'status');
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

    public function getListAll($keyword = null, $counting = false, $limit = 10, $offset = 0)
    {
        $query = $this->model
            ->where('app_name', 'LIKE', "%$keyword%");

        if (!$counting) {
            $query->select('id', 'app_name');
            if ($limit > 0) {
                $query->skip($offset)
                    ->take($limit);
            }

            $query->orderBy('app_name', 'asc');

        } else {
            return $query->count();
        }

        return $query->get();
    }

    /**
     * @param $arr
     * @return mixed
     */
    public function edit($arr)
    {
        $account = $this->model->find($arr['id']);

        if ($account != null) {
            $account->fill($arr);

            if ($account->save()) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    public function changeStatus($id, $status)
    {
        $account = $this->model->find($id);

        if ($account != null) {
            $account->status = $status;

            if ($account->save()) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * @param $arr
     * @return bool
     */
    public function store($arr)
    {
        $account = new $this->model;
        $account->fill($arr);
        $account->status = ACCOUNT_STATUS_ACTIVE;
        $account->user_id = auth()->id();

        if ($account->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function getLicense()
    {
        $accounts = Account::where('status', ACCOUNT_STATUS_ACTIVE)->get();
//        dd($accounts[0]['access_token']);
        $access_token = 'eyJ0eXAiOiJKV1QiLCJub25jZSI6IkNTeHFWSDhmQktUeTFlRUk5OEphaXJnT1VtV3U4a3JULU10cVNkRDFmS00iLCJhbGciOiJSUzI1NiIsIng1dCI6IjVPZjlQNUY5Z0NDd0NtRjJCT0hIeEREUS1EayIsImtpZCI6IjVPZjlQNUY5Z0NDd0NtRjJCT0hIeEREUS1EayJ9.eyJhdWQiOiJodHRwczovL2dyYXBoLm1pY3Jvc29mdC5jb20iLCJpc3MiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC85YjQyOTQ2MS1jNjU0LTQ3NTAtOThlYS0wNWU5YWQxMDg0YmMvIiwiaWF0IjoxNjA5NjQyNTAyLCJuYmYiOjE2MDk2NDI1MDIsImV4cCI6MTYwOTY0NjQwMiwiYWlvIjoiRTJKZ1lOQlFraGRaUEdYVmpHVkZFL01iODdaY0JnQT0iLCJhcHBfZGlzcGxheW5hbWUiOiJPZmZpY2UgMzY1IFJlc2VsbGVyIiwiYXBwaWQiOiI1N2MyYWQ1Ny00NTU0LTRjNzItYmUyOC1lNmJmNTRlOTI5ZGQiLCJhcHBpZGFjciI6IjEiLCJpZHAiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC85YjQyOTQ2MS1jNjU0LTQ3NTAtOThlYS0wNWU5YWQxMDg0YmMvIiwiaWR0eXAiOiJhcHAiLCJvaWQiOiI2NTY0MDRjMy1iZTI2LTRkNWYtOWVjMi02YzVlMmY3ZTYwMjkiLCJyaCI6IjAuQVNvQVlaUkNtMVRHVUVlWTZnWHByUkNFdkZldHdsZFVSWEpNdmlqbXYxVHBLZDBxQUFBLiIsInJvbGVzIjpbIkNoYXQuVXBkYXRlUG9saWN5VmlvbGF0aW9uLkFsbCIsIkNhbGxzLkpvaW5Hcm91cENhbGwuQWxsIiwiRWR1Um9zdGVyLlJlYWQuQWxsIiwiT25saW5lTWVldGluZ3MuUmVhZC5BbGwiLCJNYWlsLlJlYWRXcml0ZSIsIklkZW50aXR5Umlza0V2ZW50LlJlYWRXcml0ZS5BbGwiLCJPbmxpbmVNZWV0aW5ncy5SZWFkV3JpdGUuQWxsIiwiRGV2aWNlLlJlYWRXcml0ZS5BbGwiLCJVc2VyLlJlYWRXcml0ZS5BbGwiLCJEb21haW4uUmVhZFdyaXRlLkFsbCIsIkFwcGxpY2F0aW9uLlJlYWRXcml0ZS5Pd25lZEJ5IiwiU2VjdXJpdHlBY3Rpb25zLlJlYWRXcml0ZS5BbGwiLCJTZWN1cml0eUV2ZW50cy5SZWFkLkFsbCIsIkNhbGVuZGFycy5SZWFkIiwiRWR1QXNzaWdubWVudHMuUmVhZFdyaXRlLkFsbCIsIlBlb3BsZS5SZWFkLkFsbCIsIkFwcGxpY2F0aW9uLlJlYWRXcml0ZS5BbGwiLCJDYWxscy5Jbml0aWF0ZUdyb3VwQ2FsbC5BbGwiLCJHcm91cC5SZWFkLkFsbCIsIkFjY2Vzc1Jldmlldy5SZWFkV3JpdGUuQWxsIiwiRGlyZWN0b3J5LlJlYWRXcml0ZS5BbGwiLCJJZGVudGl0eVJpc2t5VXNlci5SZWFkV3JpdGUuQWxsIiwiRWR1QXNzaWdubWVudHMuUmVhZFdyaXRlQmFzaWMuQWxsIiwiTWFpbGJveFNldHRpbmdzLlJlYWQiLCJFZHVBZG1pbmlzdHJhdGlvbi5SZWFkLkFsbCIsIkNhbGxzLkpvaW5Hcm91cENhbGxBc0d1ZXN0LkFsbCIsIlRocmVhdEluZGljYXRvcnMuUmVhZFdyaXRlLk93bmVkQnkiLCJTaXRlcy5SZWFkLkFsbCIsIlNpdGVzLlJlYWRXcml0ZS5BbGwiLCJDb250YWN0cy5SZWFkV3JpdGUiLCJHcm91cC5SZWFkV3JpdGUuQWxsIiwiU2l0ZXMuTWFuYWdlLkFsbCIsIlNlY3VyaXR5RXZlbnRzLlJlYWRXcml0ZS5BbGwiLCJOb3Rlcy5SZWFkLkFsbCIsIlVzZXIuSW52aXRlLkFsbCIsIkVkdVJvc3Rlci5SZWFkV3JpdGUuQWxsIiwiRmlsZXMuUmVhZFdyaXRlLkFsbCIsIkRpcmVjdG9yeS5SZWFkLkFsbCIsIlVzZXIuUmVhZC5BbGwiLCJFZHVBc3NpZ25tZW50cy5SZWFkQmFzaWMuQWxsIiwiRWR1Um9zdGVyLlJlYWRCYXNpYy5BbGwiLCJGaWxlcy5SZWFkLkFsbCIsIk1haWwuUmVhZCIsIkNoYXQuUmVhZC5BbGwiLCJDaGFubmVsTWVzc2FnZS5SZWFkLkFsbCIsIlVzZXIuRXhwb3J0LkFsbCIsIkVkdUFzc2lnbm1lbnRzLlJlYWQuQWxsIiwiU2VjdXJpdHlBY3Rpb25zLlJlYWQuQWxsIiwiQ2FsZW5kYXJzLlJlYWRXcml0ZSIsIklkZW50aXR5Umlza3lVc2VyLlJlYWQuQWxsIiwiQWNjZXNzUmV2aWV3LlJlYWQuQWxsIiwiRWR1QWRtaW5pc3RyYXRpb24uUmVhZFdyaXRlLkFsbCIsIk1haWwuU2VuZCIsIkNoYXQuUmVhZFdyaXRlLkFsbCIsIlVzZXIuTWFuYWdlSWRlbnRpdGllcy5BbGwiLCJDaGFubmVsTWVzc2FnZS5VcGRhdGVQb2xpY3lWaW9sYXRpb24uQWxsIiwiTWFpbGJveFNldHRpbmdzLlJlYWRXcml0ZSIsIkNvbnRhY3RzLlJlYWQiLCJJZGVudGl0eVJpc2tFdmVudC5SZWFkLkFsbCIsIkF1ZGl0TG9nLlJlYWQuQWxsIiwiTWVtYmVyLlJlYWQuSGlkZGVuIiwiQ2FsbHMuQWNjZXNzTWVkaWEuQWxsIiwiUHJvZ3JhbUNvbnRyb2wuUmVhZC5BbGwiLCJTaXRlcy5GdWxsQ29udHJvbC5BbGwiLCJQcm9ncmFtQ29udHJvbC5SZWFkV3JpdGUuQWxsIiwiUmVwb3J0cy5SZWFkLkFsbCIsIkNhbGxzLkluaXRpYXRlLkFsbCIsIk5vdGVzLlJlYWRXcml0ZS5BbGwiXSwic3ViIjoiNjU2NDA0YzMtYmUyNi00ZDVmLTllYzItNmM1ZTJmN2U2MDI5IiwidGVuYW50X3JlZ2lvbl9zY29wZSI6IkFTIiwidGlkIjoiOWI0Mjk0NjEtYzY1NC00NzUwLTk4ZWEtMDVlOWFkMTA4NGJjIiwidXRpIjoidVlKV2MzSS1Ia09VNWpwX0tJY0FBUSIsInZlciI6IjEuMCIsInhtc190Y2R0IjoxNTU2MTg5NjIzfQ.p-A-I1gJwVseZaaEB9l_-U26lVYL47rPOJXoLE8osxIedVOOlcePUlio2cZVuW_c7PdfyOlnpbuT2A8KHJto3RMFPBF1uPn475b4YG-0qgKVZHw_Ktg70JtUGs7dpj9qZ41Vhzb65RIxrUEDr1q_w9EWUqKVmPuav3mfH6bur3Mi2C0mI2Q5METCt0elS-X9diafy8ikll6i-j6XrZbydY9qt1P9gnAvIZ1Nh4In_zJnz1HySXsLrEKEsGlH_URw-j1WWaVsbdBHH6quoy3qeWdutmTi4PfZNpNSEAejA2st3u7FUXrUSMHiTOtNSABQuacVaXyIQ4H-78WOUPag_w';

        $data = sendRequest(GRAPH_ROOT . "/subscribedSkus", [], $access_token, 'GET');
        if ($data != '') {
            $result = json_decode($data);

            return ($result);
        }
    }
}
