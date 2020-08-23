<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class PolicyExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithMapping
{
    use RegistersEventListeners;

    private $stt;
    private $repo;
    private $rule_type;


    public function __construct($repo, $rule_type)
    {
        $this->stt = 1;
        $this->repo = $repo;
        $this->rule_type = $rule_type;
    }

    public function collection()
    {
        return $this->repo->getList(null, $this->rule_type, false, -1);
    }

    public function map($row): array
    {
        return [
            $this->stt++,
            $row->rule_content,
            $row->rule_type,
            $row->modify_status,
            $row->protocol,
            $row->port,
            $row->created_at,
            $row->updated_at,
        ];
    }
    public function headings(): array
    {
        return
            [
                'STT',
                'Luật',
                'Loại',
                'Trạng thái',
                'Protocol',
                'Port',
                'Thời gian tạo',
                'Cập nhật cuối',
            ];
    }

    public function title(): string
    {

        return 'DANH SÁCH LUẬT '. strtoupper($this->rule_type);
    }
}
