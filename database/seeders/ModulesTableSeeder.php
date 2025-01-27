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
                    ],
                    [
                        'name' => 'outgoing',
                        'path' => '/admin/manifests/outgoing/create',
                        'description' => null,
                        'active' => 0,
                        'icon' => null,
                    ]
                ],
            ],
        ];

        foreach ($modules as $moduleData) {
            // Ensure required fields are present
            $parentModule = Module::updateOrCreate(
                ['path' => $moduleData['path']],
                [
                    'name' => $moduleData['name'] ?? 'Unnamed Module',
                    'type' => $moduleData['type'] ?? null,
                    'description' => $moduleData['description'] ?? null,
                    'parent_id' => $moduleData['parent_id'] ?? null,
                    'active' => $moduleData['active'] ?? 0,
                    'icon' => $moduleData['icon'] ?? null,
                ]
            );

            if (!empty($moduleData['children'])) {
                foreach ($moduleData['children'] as $childData) {
                    $parentModule->children()->updateOrCreate(
                        ['path' => $childData['path']],
                        [
                            'name' => $childData['name'] ?? 'Unnamed Child Module',
                            'description' => $childData['description'] ?? null,
                            'parent_id' => $parentModule->id,
                            'active' => $childData['active'] ?? 0,
                            'icon' => $childData['icon'] ?? null,
                        ]
                    );
                }
            }
        }
    }
}
