<?php


namespace App\Controllers;


use App\Models\CheckAuth;
use App\Models\User\CreateOrder;
use App\Models\Roles;
use App\Models\User\DeleteOrderById;
use App\Models\User\GetAllDepartmentOrders;
use App\Models\User\GetAllDepartmentOrdersPerMonth;
use App\Models\User\getAllOrdersByDate;
use App\Models\User\GetDepartmentInfo;
use App\Models\User\GetOrderById;
use App\Models\User\UpdateOrderById;
use Core\Controller;

class UserController extends Controller
{

    public function createOrderAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission(action: 'create-order')) {
            $createOrder = new CreateOrder();
            $createOrder->create() ? sendResponse(code: 200) : $createOrder::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

    public function getAllDepartmentOrdersAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission(action: 'get-all-department-orders')){
            $getAllOrders = new GetAllDepartmentOrders();
            $result = $getAllOrders->getAllDepartmentOrders();
            $result ? sendResponse(200, $result) : $getAllOrders::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

    public function getAllOrdersPerMonthAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission(action: 'get-all-department-orders-per-month')){
            $getAllOrdersPerMonth = new GetAllDepartmentOrdersPerMonth();
            $result = $getAllOrdersPerMonth->getAllDepartmentOrdersPerMonth();
            $result ? sendResponse(200, $result) : $getAllOrdersPerMonth::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

    public function getOrderByIdAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission(action: 'get-order-by-id')){
            $getOrderById = new GetOrderById();
            $result = $getOrderById->getOrderById();
            $result ? sendResponse(200, $result) : $getOrderById::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

    public function updateOrderByIdAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission(action: 'update-order-by-id')) {
            $updateOrderById = new UpdateOrderById();
            $updateOrderById->updateOrder() ? sendResponse(code: 200) : $updateOrderById::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

    public function deleteOrderAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission(action: 'delete-order-by-id')){
            $deleteOrderById = new DeleteOrderById();
            $deleteOrderById->deleteOrderById() ? sendResponse(code: 200) : $deleteOrderById::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

    public function getAllOrdersByDateAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission(action: 'get-all-orders-by-date')){
            $getAllOrdersByDate = new GetAllOrdersByDate();
            $result = $getAllOrdersByDate->getAllOrdersByDate();
            $result ? sendResponse(code: 200, data: $result) : $getAllOrdersByDate::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

    public function getDepartmentInfoAction()
    {
        if(CheckAuth::checkAuth() && Roles::hasPermission(action: 'get-department-info')){
            $getDepartmentInfo = new GetDepartmentInfo();
            $result = $getDepartmentInfo->getDepartmentInfo();
            $result ? sendResponse(code: 200, data: $result) : $getDepartmentInfo::getError();
        }else CheckAuth::getError() || Roles::getError();
    }

}