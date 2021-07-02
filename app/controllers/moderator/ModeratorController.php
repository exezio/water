<?php

namespace App\Controllers\Moderator;

use App\Models\CheckAuth;
use App\Models\Moderator\Cabinet;
use App\Models\Roles;
use Core\Controller;


class ModeratorController extends Controller
{

    public function cabinetAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission('cabinet-moderator'))
        {
            $cabinet = new Cabinet();
            $result = $cabinet->cabinet() ? sendResponse(code: 200) : $cabinet::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

}