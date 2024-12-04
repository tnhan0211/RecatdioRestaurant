@extends('admin.layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Chi tiết đặt bàn #{{ $booking->id }}</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-primary">Trở về</a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Thông tin đặt bàn</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th style="width:200px">Tên khách hàng:</th>
                                <td>{{ $booking->name }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại:</th>
                                <td>{{ $booking->phone }}</td>
                            </tr>
                            @if($booking->user_id)
                            <tr>
                                <th>Email:</th>
                                <td>{{ $booking->user->email }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Ngày đặt bàn:</th>
                                <td>{{ $booking->booking_date->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Số người:</th>
                                <td>{{ $booking->number_of_people }}</td>
                            </tr>
                            <tr>
                                <th>Yêu cầu đặc biệt:</th>
                                <td>{{ $booking->special_request ?: 'Không có' }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td>
                                    <select class="form-control booking-status" data-id="{{ $booking->id }}">
                                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            @if($booking->bookingMenus->count() > 0)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách món ăn</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Món ăn</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->bookingMenus as $item)
                                <tr>
                                    <td>{{ $item->menu->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price) }}đ</td>
                                    <td>{{ number_format($item->subtotal) }}đ</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Tổng cộng:</th>
                                    <th>{{ number_format($booking->total_amount) }}đ</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@section('customjs')
<script>
$(document).ready(function() {
    $('.booking-status').change(function() {
        var bookingId = $(this).data('id');
        var status = $(this).val();
        
        $.ajax({
            url: '/admin/bookings/' + bookingId + '/status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                if(response.status) {
                    alert('Cập nhật trạng thái thành công');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra');
            }
        });
    });
});
</script>
@endsection 