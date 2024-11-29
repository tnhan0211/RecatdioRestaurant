<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn đặt bàn của người dùng
     */
    public function index(): View
    {
        // Tạm thời trả về view với mảng orders rỗng
        // cho đến khi bạn tạo model Order và migration
        $orders = [];
        
        return view('orders.index', compact('orders'));
    }
}
