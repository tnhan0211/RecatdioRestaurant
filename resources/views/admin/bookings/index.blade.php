@extends('admin.layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Danh sách đặt bàn</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Bộ lọc -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('admin.bookings.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">Đặt lại</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Ngày đặt</th>
                            <th>Số người</th>
                            <th>Loại đặt bàn</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $booking->id }}</td>
                            <td>
                                {{ $booking->name }}
                                @if($booking->user_id)
                                    <br><small class="text-muted">Tài khoản: {{ $booking->user->email }}</small>
                                @endif
                            </td>
                            <td>{{ $booking->phone }}</td>
                            <td>{{ $booking->booking_date->format('d/m/Y H:i') }}</td>
                            <td>{{ $booking->number_of_people }}</td>
                            <td>
                                @if($booking->bookingMenus->count() > 0)
                                    <span class="badge badge-success">Đặt kèm món</span>
                                @else
                                    <span class="badge badge-info">Chỉ đặt bàn</span>
                                @endif
                            </td>
                            <td>
                                @if($booking->bookingMenus->count() > 0)
                                    {{ number_format($booking->total_amount) }}đ
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <select class="form-control booking-status" data-id="{{ $booking->id }}">
                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                            </td>
                            <td>
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-danger btn-sm delete-booking" data-id="{{ $booking->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Không có đơn đặt bàn nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</section>
@endsection

@section('customjs')
<script>
$(document).ready(function() {
    // Cập nhật trạng thái
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

    // Xóa đặt bàn
    $('.delete-booking').click(function() {
        if(confirm('Bạn có chắc chắn muốn xóa đơn đặt bàn này?')) {
            var bookingId = $(this).data('id');
            $.ajax({
                url: '/admin/bookings/' + bookingId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.status) {
                        window.location.reload();
                    }
                }
            });
        }
    });
});
</script>
@endsection 