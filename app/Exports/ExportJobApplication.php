<?php

namespace App\Exports;

use App\JobApplication;
use Maatwebsite\Excel\Concerns\FromCollection; 
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportJobApplication implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect([
            [

                'full_name'=>'Akhilesh',
                'lastname'=>'yadav',
                'phone'=>'9999999999',
                'email'=>'demo@gmail.com',
                'fatherfirst'=>'Ramajee',
                 'fatherlast'=>'Yadav',
	        'qualification'=>'BCA',
                'subqualification'=>'Computer',
                'relevent_exp'=>'1',
                'total_exp'=>'2',                
                'skill'=>'laravel,php',
	            ]
        ]);

    }

    public function headings(): array
    {
        return [
            'First Name*',
            'Last Name',
            'Phone *',
            'Email *',
            'Father First Name',
            'Father Last Name',
            'Qualification',
            'Sub Qualification',
            'Relevent Exp',
            'Total Exp',
            'Skill',
            
            
        ];
    }

}
