<?php

namespace App\Imports;

use App\Models\Policy;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class PolicyImport implements ToModel, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $rule_type;

    public function __construct($rule_type)
    {
        $this->rule_type = $rule_type;
    }

    public function model(array $row)
    {
        return new Policy([
            'rule_content' => $row['luat'],
            'rule_type' => $row['loai'],
            'modify_status' => $row['trang_thai'] ? $row['trang_thai'] : 1,
            'protocol' => $row['protocol'],
            'port' => $row['port'],
        ]);
    }

    public function rules(): array
    {
        return [
            'luat' => 'required|unique:rules,rule_content',
            'loai' => ['required',Rule::in([RULE_IPV4, RULE_IPV6, RULE_DOMAIN, RULE_DNS, RULE_URL])],
            'trang_thai' => ['nullable',Rule::in([STATUS_ACTIVE, STATUS_DISABLE, STATUS_ACTIVE])],
            'port' => ['nullable','regex:/^(All|6553[0-5]|655[1-2][0-9]|65[0-4][0-9]{2}|6[0-4][0-9]{3}|[0-5][0-9]{4}|[1-9][0-9]{0,3})$/'],
            'protocol' => ['nullable',Rule::in(PROTOCOL_ALLOW)],

            '*.luat' => 'required|unique:rules,rule_content',
            '*.loai' => ['required',Rule::in([RULE_IPV4, RULE_IPV6, RULE_DOMAIN, RULE_DNS, RULE_URL])],
            '*.trang_thai' => ['nullable',Rule::in([STATUS_ACTIVE, STATUS_DISABLE, STATUS_ACTIVE])],
            '*.port' =>['nullable','regex:/^(All|6553[0-5]|655[1-2][0-9]|65[0-4][0-9]{2}|6[0-4][0-9]{3}|[0-5][0-9]{4}|[1-9][0-9]{0,3})$/'],
            '*.protocol' => ['nullable',Rule::in(PROTOCOL_ALLOW)],
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}