<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'category-list',
            'category-create',
            'category-edit',
            'category-delete',
            'tag-list',
            'tag-create',
            'tag-edit',
            'tag-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'job-list',
            'job-create',
            'job-edit',
            'job-delete',
            'assessment-list',
            'assessment-create',
            'assessment-edit',
            'assessment-delete',
            'question-list',
            'question-delete',
            'question-edit',
            'question-create',
            'answer-list',
            'answer-delete',
            'answer-create',
            'answer-edit',

        ];
        foreach ($permissions as $permission) {
            // Check if the permission already exists
            if (!Permission::where('name', $permission)->where('guard_name', 'web')->exists()) {
                Permission::create(['name' => $permission, 'guard_name' => 'web']);
            }
        }
    }
}
