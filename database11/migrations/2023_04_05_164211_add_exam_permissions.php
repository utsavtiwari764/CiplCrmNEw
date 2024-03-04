<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Role;
use App\Permission;
use App\Module;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Module::insert([
            ['id' => 13, 'module_name' => 'exams', 'description' => '']
        ]);

        $permissions = [
            ['name' => 'add_exam', 'display_name' => 'Add Exam', 'module_id' => 13],
            ['name' => 'view_exam', 'display_name' => 'View Exam', 'module_id' => 13],
            ['name' => 'edit_exam', 'display_name' => 'Edit Exam', 'module_id' => 13],
            ['name' => 'delete_exam', 'display_name' => 'Delete Exam', 'module_id' => 13],
        ];

        $admin = Role::where('name', 'admin')->first();
        foreach ($permissions as $permission){

            $create = Permission::create($permission);
            $admin->attachPermission($create);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::where('module_id', 13)->delete();
    }
};
