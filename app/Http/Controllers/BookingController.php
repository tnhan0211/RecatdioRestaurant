<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingMenu;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // Hiển thị danh sách booking của user
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $bookings = $user->bookings()->latest()->paginate(10);
        
        return view('bookings.index', [
            'bookings' => $bookings
        ]);
    }

    // Hiển thị form tạo booking mới
    public function create()
    {
        $categories = Category::with(['menu' => function($query) {
            $query->where('status', 1)
                  ->orderBy('position');
        }])
        ->where('status', 1)
        ->get();
        
        return view('front.booking', compact('categories'));
    }

    // Lưu booking mới
    public function store(Request $request)
    {
        // Kiểm tra nếu khách vãng lai và chọn đặt bàn kèm món ăn
        if (!Auth::check() && $request->booking_type === 'with_menu') {
            return redirect()->route('front.booking')
                ->with('error', 'Bạn cần đăng nhập để đặt bàn kèm món ăn.');
        }

        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'regex:/^([0-9\s\-\+\(\)]*)$/',  // Chỉ cho phép số, dấu +, - và ()
                'min:10',
                'max:11'
            ],
            'booking_date' => [
                'required',
                'date',
                'after:' . now()->addHours(0.5)->format('Y-m-d H:i:s')  // Đặt bàn trước ít nhất 1 tiếng
            ],
            'number_of_people' => 'required|integer|min:1|max:10',
            'special_request' => 'nullable|string|max:500',
            'booking_type' => 'required|in:only_table,with_menu',
            
            // Validate menu items nếu là đặt bàn kèm món
            'menu_items' => 'required_if:booking_type,with_menu|array',
            'menu_items.*.selected' => 'boolean',
            'menu_items.*.quantity' => 'required_if:menu_items.*.selected,1|integer|min:1|max:10',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'name.max' => 'Họ tên không được vượt quá 255 ký tự',
            
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.regex' => 'Số điện thoại không hợp lệ',
            'phone.min' => 'Số điện thoại phải có ít nhất 10 số',
            'phone.max' => 'Số điện thoại không được vượt quá 15 số',
            
            'booking_date.required' => 'Vui lòng chọn ngày giờ đặt bàn',
            'booking_date.after' => 'Vui lòng đặt bàn trước ít nhất 30 phút',
            
            'number_of_people.required' => 'Vui lòng nhập số người',
            'number_of_people.min' => 'Số người phải ít nhất là 1',
            'number_of_people.max' => 'Số người không được vượt quá 10',
            
            'special_request.max' => 'Yêu cầu đặc biệt không được vượt quá 500 ký tự',
            
            'menu_items.required_if' => 'Vui lòng chọn ít nhất một món ăn',
            'menu_items.*.quantity.min' => 'Số lượng món ăn phải ít nhất là 1',
            'menu_items.*.quantity.max' => 'Số lượng món ăn không được vượt quá 10',
        ]);

        try {
            DB::beginTransaction();

            // Tạo booking
            $booking = Booking::create([
                'user_id' => Auth::id(), // Có thể null nếu khách vãng lai
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'booking_date' => $validated['booking_date'],
                'number_of_people' => $validated['number_of_people'],
                'special_request' => $validated['special_request'],
                'status' => 'pending',
                'booking_type' => $validated['booking_type']
            ]);

            // Nếu đặt bàn kèm món ăn
            if ($request->booking_type === 'with_menu') {
                $totalAmount = 0;
                
                foreach ($request->menu_items as $menuId => $item) {
                    if (isset($item['selected']) && $item['selected']) {
                        $menu = Menu::findOrFail($menuId);
                        $subtotal = $menu->price * $item['quantity'];

                        BookingMenu::create([
                            'booking_id' => $booking->id,
                            'menu_id' => $menuId,
                            'quantity' => $item['quantity'],
                            'price' => $menu->price,
                            'subtotal' => $subtotal
                        ]);

                        $totalAmount += $subtotal;
                    }
                }

                $booking->update(['total_amount' => $totalAmount]);
            }

            DB::commit();

            // Lưu dữ liệu vào session
            $bookingData = $request->all();
            session(['booking_data' => $bookingData]);

            // Chuyển hướng đến trang xác nhận
            return redirect()->route('front.confirm');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi đặt bàn. Vui lòng thử lại.')
                ->withInput();
        }
    }

    // Hủy booking
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return redirect()->route('bookings.index')
                ->with('error', 'Bạn không có quyền hủy đặt bàn này.');
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('bookings.index')
            ->with('success', 'Đặt bàn đã được hủy.');
    }

    public function showConfirmation(Request $request)
    {
        // Validate session data
        $bookingData = session('booking_data');
        if (!$bookingData) {
            return redirect()->route('front.booking')
                ->with('error', 'Không tìm thấy thông tin đặt bàn');
        }

        // Tính tổng tiền và tiền đặt cọc
        $totalAmount = 0;
        if (isset($bookingData['menu_items'])) {
            foreach ($bookingData['menu_items'] as $item) {
                $menu = Menu::find($item['menu_id']);
                $totalAmount += $menu->price * $item['quantity'];
            }
        }
        $depositAmount = $totalAmount * 0.3; // 30% đặt cọc

        return view('front.confirm', compact('bookingData', 'totalAmount', 'depositAmount'));
    }

    public function processPayment(Request $request)
    {
        $bookingData = session('booking_data');
        $amount = $request->input('amount'); // Số tiền đặt cọc

        // Khởi tạo MoMo
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = "MOMOXXX"; // Thay bằng partner code của bạn
        $accessKey = "xxx"; // Thay bằng access key của bạn
        $secretKey = "xxx"; // Thay bằng secret key của bạn
        $orderInfo = "Đặt cọc bàn ăn";
        $redirectUrl = route('front.booking.success');
        $ipnUrl = route('front.booking.ipn'); // URL nhận IPN từ MoMo
        $requestId = time() . "";
        $orderId = time() . "";

        // Tạo chữ ký
        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $amount .
            "&extraData=" .
            "&ipnUrl=" . $ipnUrl .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $partnerCode .
            "&redirectUrl=" . $redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=captureWallet";
        
        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => '',
            'requestType' => 'captureWallet',
            'signature' => $signature
        ];

        // Gửi request đến MoMo
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        ]);
        
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response = json_decode($result, true);

        if ($status == 200 && isset($response['payUrl'])) {
            return redirect($response['payUrl']);
        }

        return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo thanh toán');
    }
} 