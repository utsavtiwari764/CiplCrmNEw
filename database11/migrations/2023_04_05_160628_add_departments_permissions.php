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
            ['id' => 12, 'module_name' => 'department', 'description' => '']
        ]);

        $permissions = [
            ['name' => 'add_department', 'display_name' => 'Add Department', 'module_id' => 12],
            ['name' => 'view_department', 'display_name' => 'View Department', 'module_id' => 12],
            ['name' => 'edit_department', 'display_name' => 'Edit Department', 'module_id' => 12],
            ['name' => 'delete_department', 'display_name' => 'Delete Department', 'module_id' => 12],
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
         Permission::where('module_id', 12)->delete();
    }
};
