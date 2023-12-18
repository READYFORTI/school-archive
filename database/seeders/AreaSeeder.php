<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = (object) [
            [
                'name' => 'Administration',
                'type' => 'office',
                'children' => [
                    'Library',
                    'Clinic',
                    'Registrar',
                    'Cashier',
                ]
            ],
            [
                'name'=> 'Academics',
                'type' => 'institute',
                'children' => [
                    [
                        'name' => 'IAAS',
                        'type' => 'program',
                        'children' => [
                            ['name' => 'BSAF', 'description' => 'Bachelor of Science in Agroforestry'],
                            ['name' => 'BSFAS', 'description' => 'Bachelor of Science in Fisheries and Aquatic Sciences'],
                            ['name' => 'BSFT', 'description' => 'Bachelor of Science in Food Technology'],
                            ['name' => 'BSMB', 'description' => 'Bachelor of Science in Marine Biology'],
                        ],
                    ],
                    [
                        'name' => 'IC',
                        'type' => 'program',
                        'children' => [
                            ['name' => 'BSIS', 'description' => 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'],
                            ['name' => 'BSIT', 'description' => 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY'],
                        ],
                    ],
                    [
                        'name' => 'ILEGG',
                        'type' => 'program',
                        'children' => [
                            ['name' => 'BPA', 'description' => 'BACHELOR OF PUBLIC ADMINISTRATION'],
                            ['name' => 'BSDRM', 'description' => 'BACHELOR OF SCIENCE IN DISASTER RESILIENCY AND MANAGEMENT'],
                            ['name' => 'BS ENTREP', 'description' => 'BACHELOR OF SCIENCE IN ENTREPRENEURSHIP'],
                            ['name' => 'BSSW', 'description' => 'BACHELOR OF SCIENCE IN SOCIAL WORK'],
                            ['name' => 'BSTM', 'description' => 'BACHELOR OF SCIENCE IN TOURISM MANAGEMENT'],
                        ],
                    ],
                    [
                        'name' => 'ITED-BACHELOR OF ARTS IN COMMUNICATION (BACOMM)',
                        'type' => 'program',
                        'children' => [
                            ['name' => 'BSeD', 'description' => 'BACHELOR OF SECONDARY EDUCATION (BSeD) Major in English, Math, and Sciences'],
                            ['name' => 'BTLEd', 'description' => 'BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION'],
                        ],
                    ],
                    [
                        'name' => 'IADS-Doctor of Philosophy in Educational Management (PHD EM)',
                        'type' => 'program',
                        'children' => [
                            ['name' => 'MFM AQUA, MFM FP', 'description' => 'Master in Fisheries Management Major in Aquaculture Technology and Fish Processing'],
                            ['name' => 'MABE ENG', 'description' => 'Master of Arts in Basic Education English'],
                            ['name' => 'MAEM', 'description' => 'Master of Arts in Educational Management'],
                            ['name' => 'MSMB', 'description' => 'Master of Science in Marine Biodiversity'],
                            ['name' => 'MST BIO', 'description' => 'Master of Science Teaching in Biology'],
                            ['name' => 'MST MATH', 'description' => 'Master of Science Teaching in Mathematics'],
                        ],
                    ]
                ]
            ],
        ];

        foreach($areas as $data) {
            $this->saveArea($data, null);
        }
    }

    private function saveArea($data, $parent_id = null, $area_type = null) {
        $area_name = $data['name'] ?? $data;
        $area_description = $data['description'] ?? $area_name;
        $area = !empty($parent_id) ? Area::where('area_name', $area_name)->where('parent_area', $parent_id)->first() : Area::where('area_name', $area_name)->first();
        if(empty($area)) {
            $area = Area::create([
                'area_name' => $area_name,
                'area_description' => ucwords($area_description),
                'parent_area' => $parent_id,
                'type' => $area_type
            ]);

            if(in_array($area_type, ['program', 'office'])) {
                // Area::create([
                //     'area_name' => 'Process 1',
                //     'area_description' => ucwords('Process 1'),
                //     'parent_area' => $area->id,
                //     'type' => 'process'
                // ]);
                
                Area::create([
                    'area_name' => $area_name. ' Process',
                    'area_description' => ucwords($area_name. ' Process'),
                    'parent_area' => $area->id,
                    'type' => 'process'
                ]);
                Area::create([
                    'area_name' => 'Same '.' Process',
                    'area_description' => ucwords($area_name. ' Process'),
                    'parent_area' => $area->id,
                    'type' => 'process'
                ]);
            }
        }

        if(!empty($data['children'])) {
            foreach($data['children'] as $child) {
                $area_type = !empty($data['type']) ? $data['type'] : $area_type; 
                $this->saveArea($child, $area->id, $area_type);
            }
        }

        return $area->id;
    }

    // IAAS-
    // Bachelor of Science in Agroforestry (BSAF)

    // Bachelor of Science in Fisheries and Aquatic Sciences (BSFAS)
    // Bachelor of Science in Food Technology (BSFT)
    // Bachelor of Science in Marine Biology (BSMB)

    // IC-
    // BACHELOR OF SCIENCE IN INFORMATION SYSTEMS (BSIS)
    // BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY (BSIT)

    // ILEGG-
    // BACHELOR OF PUBLIC ADMINISTRATION (BPA)
    // BACHELOR OF SCIENCE IN DISASTER RESILIENCY AND MANAGEMENT (BSDRM)
    // BACHELOR OF SCIENCE IN ENTREPRENEURSHIP (BS ENTREP)
    // BACHELOR OF SCIENCE IN SOCIAL WORK (BSSW)
    // BACHELOR OF SCIENCE IN TOURISM MANAGEMENT (BSTM)

    // ITED-BACHELOR OF ARTS IN COMMUNICATION (BACOMM)
    // BACHELOR OF SECONDARY EDUCATION (BSeD) Major in English, Math, and Sciences
    // BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION (BTLEd)

    // IADS-Doctor of Philosophy in Educational Management (PHD EM)
    // Master in Fisheries Management Major in Aquaculture Technology and Fish Processing (MFM AQUA, MFM FP)
    // Master of Arts in Basic Education English (MABE ENG)
    // Master of Arts in Educational Management (MAEM)
    // Master of Science in Marine Biodiversity (MSMB)
    // Master of Science Teaching in Biology (MST BIO)
    // Master of Science Teaching in Mathematics (MST MATH)
}
