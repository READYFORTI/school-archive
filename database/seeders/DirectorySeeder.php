<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Area;
use App\Models\Directory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DirectorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::get()->where('name', '!=', 'Administrator')->pluck('role_name');
        $directories = [
            [
                'name' => 'Manuals',
                'sub_directory'=>['Procedures Manual','System Control','Quality Policy'],
                'automatic_child' => true,
            ],
            [
                'name' => 'Evidences',
                'automatic_child' => true,
            ],
            [
                'name' => 'Templates',
                'sub_directory' => $roles
            ],
            [
                'name' => 'Audit Reports'
            ],
            [
                'name' => 'Consolidated Audit Reports',
            ],
            [
                'name' => 'Survey Reports'
            ],
        ];

        $sub_directory = Area::whereNull('parent_area')->get();

        foreach($directories as $item) {
            $directory = Directory::where('name', $item['name'])->first();
            if(empty($directory)) {
                $directory = Directory::create([
                    'name' => $item['name']
                ]);
            }

            if(!empty($item['sub_directory']))
            {
                foreach($item['sub_directory'] as $role)
                {
                    $child_directory = Directory::create([
                        'name' => $role,
                        'parent_id' => $directory->id
                    ]);
                    if($item['name'] == 'Templates' && 
                        in_array($role, ['Process Owner', 'Document Control Custodian'])
                    ) {
                        foreach($sub_directory as $child) {
                            $dir = Directory::create([
                                'name' => $child->area_name,
                                'parent_id' => $child_directory->id,
                                'area_dependent' => true,
                                'area_id' => $child->id
                            ]);

                            if(!empty($child->children)) {
                                foreach($child->children as $row) {
                                    $this->saveAreaDirectory($row, $dir->id);
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($item['automatic_child'])) {
                foreach($sub_directory as $child) {
                    $dir = Directory::create([
                        'name' => $child->area_name,
                        'parent_id' => $directory->id,
                        'area_dependent' => true,
                        'area_id' => $child->id
                    ]);

                    if(!empty($child->children)) {
                        foreach($child->children as $row) {
                            $this->saveAreaDirectory($row, $dir->id);
                        }
                    }
                }
            }
        }

        Directory::query()
        ->whereIn('name',['Administration','Academics'])
        ->where('parent_id',1)
        ->update([
            'parent_id'=>2
        ]);
    }

    private function saveAreaDirectory($data, $parent_id) {
        $area_name = $data->area_name;
        // $years = ['2021', '2022', '2023'];
        $dir = Area::where('area_name', $area_name)->where('parent_area', $parent_id)->first();
        if(empty($dir)) {
            $dir = Directory::create([
                'name' => $area_name,
                'parent_id' => $parent_id,
                'area_dependent' => true,
                'area_id' => $data->id
            ]);

            // if(!empty($data->type) && $data->type == 'program')
            // {
            //     for($y = 2021; $y <= date('Y'); $y++) {
            //         $year = Directory::create([
            //             'name' => $y,
            //             'parent_id' => $dir->id
            //         ]);

            //         Directory::create([
            //             'name' => '1st Semester',
            //             'parent_id' => $year->id
            //         ]);

            //         Directory::create([
            //             'name' => '2nd Semester',
            //             'parent_id' => $year->id
            //         ]);
            //     }
            // }
        }

        if(!empty($data->children)) {
            foreach($data->children as $child) {
                $this->saveAreaDirectory($child, $dir->id);
            }
        }

        return $dir->id;
    }
}
