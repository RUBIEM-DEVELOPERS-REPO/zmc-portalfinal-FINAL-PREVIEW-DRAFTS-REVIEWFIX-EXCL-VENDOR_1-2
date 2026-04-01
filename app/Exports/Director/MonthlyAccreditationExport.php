<?php

namespace App\Exports\Director;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class MonthlyAccreditationExport implements FromCollection, WithHeadings, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect([]);
        
        // Add monthly trends
        foreach ($this->data['monthly_trends'] as $trend) {
            $rows->push([
                'Month' => $trend->month,
                'Submitted' => $trend->total_submitted,
                'Approved' => $trend->total_approved,
                'Returned for Correction' => $trend->total_rejected,
            ]);
        }
        
        return $rows;
    }

    public function headings(): array
    {
        return ['Month', 'Submitted', 'Approved', 'Returned for Correction'];
    }

    public function title(): string
    {
        return 'Monthly Accreditation Report';
    }
}
