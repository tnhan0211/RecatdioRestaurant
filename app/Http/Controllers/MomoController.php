<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingMenu;
use App\Models\Menu;
use Illuminate\Http\Request;

class MomoController extends Controller
{
    public function handleIPN(Request $request)
    {
        // Verify signature from MoMo
        $secretKey = "xxx"; // Thay bằng secret key của bạn
        
        // Xử lý kết quả thanh toán
        if ($request->resultCode == 0) {
            // Thanh toán thành công
            // Tạo booking từ session data
            $bookingData = session('booking_data');
            
            // Lưu booking và booking_menu items
            // ... (code từ phần store cũ)
            
            // Xóa session
            session()->forget('booking_data');
            
            return response()->json(['message' => 'success']);
        }
        
        return response()->json(['message' => 'failed']);
    }
} 