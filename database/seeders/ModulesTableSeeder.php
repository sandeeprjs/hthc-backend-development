<?php

namespace Database\Seeders;

use App\Module;
use Illuminate\Database\Seeder;

class ModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = [
            [
                'name' => 'bookings',
                'type' => null,
                'description' => null,
                'path' => '/admin/bookings',
                'parent_id' => null,
                'active' => 1,
                'icon' => 'booking.png',
                'children' => [],
            ],
            [
                'name' => 'deliveries',
                'type' => null,
                'description' => null,
                'path' => '/admin/deliveries',
                'parent_id' => null,
                'active' => 1,
                'icon' => 'delivery.png',
                'children' => [],
            ],
            [
                'name' => 'manifests',
                'type' => null,
                'description' => null,
                'path' => '/admin/manifests',
                'parent_id' => null,
                'active' => 1,
                'icon' => 'dispatch.png',
                'children' => [
                    [
                        'name' => 'incoming',
                        'path' => '/admin/manifests/incoming/create',
                        'description' => null,
                        'active' => 0,
                        'icon' => null,
                        'group' => null,
                    ],
                    [
                        'name' => 'outgoing',
                        'path' => '/admin/manifests/outgoing/create',
                        'description' => null,
                        'active' => 0,
                        'icon' => null,
                        'group' => null,
                    ]
                ],
            ],
            // Additional module entries here...
        ];

        foreach ($modules as $moduleData) {
            $parentModule = Module::updateOrCreate(
                ['path' => $moduleData['path']],
                [
                    'name' => $moduleData['name'],
                    'type' => $moduleData['type'],
                    'description' => $moduleData['description'],
                    'parent_id' => $moduleData['parent_id'],
                    'active' => $moduleData['active'],
                    'icon' => $moduleData['icon']
                ]
            );

            if (!empty($moduleData['children'])) {
                foreach ($moduleData['children'] as $childData) {
                    $parentModule->children()->updateOrCreate(
                        ['path' => $childData['path']],
                        [
                            'name' => $childData['name'],
                            'description' => $childData['description'],
                            'parent_id' => $parentModule->id,
                            'active' => $childData['active'],
                            'icon' => $childData['icon']
                        ]
                    );
                }
            }
        }
    }
}
