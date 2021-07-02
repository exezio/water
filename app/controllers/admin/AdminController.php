<?php


namespace App\Controllers\Admin;


use App\Models\Admin\addPermission;
use App\Models\Admin\Cabinet;
use App\Models\Admin\GetPermissions;
use App\Models\Admin\PutPermission;
use App\Models\Admin\DeletePermission;
use App\Models\Admin\CreateUser;
use App\Models\CheckAuth;
use App\Models\Roles;
use Core\Controller;

class AdminController  extends Controller
{

    public function addPermissionAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission('add-permission'))
        {
            $addPermission = new addPermission();
            $addPermission->addPermission() ? sendResponse(code: 200) : $addPermission::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

    public function getPermissionsAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission('get-permissions'))
        {
            $getPermissions = new GetPermissions();
            $result = $getPermissions->getPermissions();
            $result ? sendResponse(code: 200, data: $result) : $getPermissions::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

    public function putPermissionAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission('put-permission'))
        {
            $putPermission = new PutPermission();
            $putPermission->putPermission() ? sendResponse(code: 200) : $putPermission::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

    public function deletePermissionAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission('delete-permission'))
        {
            $deletePermission = new DeletePermission();
            $deletePermission->deletePermission() ? sendResponse(code: 200) : $deletePermission::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

    public function getAllAction()
    {
        echo "ADMIN CONTROLLER";
    }

    public function createUserAction()
    {

        if(CheckAuth::checkAuth() && Roles::hasPermission('create-user'))
        {
            $createUser = new CreateUser();
            $createUser->createUser() ? sendResponse(code: 200) : $createUser::getError();
        }else CheckAuth::getError() || Roles::getError();
//            if(CheckAuth::checkAuth()){
//                $users = new CreateUser();
//                $users->createUser() ? sendResponse(200) : $users::getError();
//            }else CheckAuth::getError();
    }

    public function cabinetAction()
    {
        debug('dsa');
        if(CheckAuth::checkAuth() && Roles::hasPermission('cabinet-moderator'))
        {
            $cabinet = new Cabinet();
            $cabinet->cabinet() ? sendResponse(code: 200) : $cabinet::getError();
        }else CheckAuth::getError() || Roles::getError();

    }

}