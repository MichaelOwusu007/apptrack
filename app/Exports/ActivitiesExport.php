<?php

namespace App\Exports;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ActivitiesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private readonly array $filters = []) {}

    public function query()
    {
        $query = Activity::with(['assignedUser', 'creator', 'updates']);

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('activity_date', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('activity_date', '<=', $this->filters['date_to']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (!empty($this->filters['priority'])) {
            $query->where('priority', $this->filters['priority']);
        }
        if (!empty($this->filters['assigned_to'])) {
            $query->where('assigned_to', $this->filters['assigned_to']);
        }

        return $query->orderBy('activity_date', 'desc');
    }

    public function headings(): array
    {
        return [
            '#',
            'Title',
            'Description',
            'Activity Date',
            'Type',
            'Priority',
            'Status',
            'Assigned To',
            'Created By',
            'Remarks',
            'Total Updates',
            'Last Updated By',
            'Last Updated At',
            'Created At',
        ];
    }

    public function map($activity): array
    {
        static $i = 0;
        $i++;
        $lastUpdate = $activity->updates->first();

        return [
            $i,
            $activity->title,
            $activity->description,
            $activity->activity_date->format('Y-m-d'),
            $activity->activity_type_label,
            strtoupper($activity->priority),
            strtoupper(str_replace('_', ' ', $activity->status)),
            $activity->assignedUser?->full_name ?? 'Unassigned',
            $activity->creator?->full_name ?? '—',
            $activity->remarks,
            $activity->updates->count(),
            $lastUpdate?->personnel_name ?? '—',
            $lastUpdate?->created_at->format('Y-m-d H:i:s') ?? '—',
            $activity->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'    => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Activity Report';
    }
}
