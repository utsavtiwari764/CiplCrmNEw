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
            ['id' => 14, 'module_name' => 'qualification', 'description' => '']
        ]);

        $permissions = [
            ['name' => 'add_qualification', 'display_name' => 'Add Qualification', 'module_id' => 14],
            ['name' => 'view_qualification', 'display_name' => 'View Qualification', 'module_id' => 14],
            ['name' => 'edit_qualification', 'display_name' => 'Edit Qualification', 'module_id' => 14],
            ['name' => 'delete_qualification', 'display_name' => 'Delete Qualification', 'module_id' => 14],
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
        Permission::where('module_id', 14)->delete();
    }
};
