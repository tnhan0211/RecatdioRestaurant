<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'bookingMenus']);

        // Xử lý tìm kiếm
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        // Xử lý lọc theo trạng thái
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Xử lý lọc theo ngày
        if ($request->has('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->orderBy('booking_date', 'desc')
                         ->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'bookingMenus.menu'])
                         ->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật trạng thái đặt bàn thành công'
        ]);
    }

    public function destroy($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $booking->bookingMenus()->delete(); // Xóa các món ăn liên quan
            $booking->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Xóa đặt bàn thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi xóa đặt bàn: ' . $e->getMessage()
            ], 500);
        }
    }
}
