@extends('layouts.app')

@section('title', 'Lịch sử đặt bàn')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Lịch sử đặt bàn</h2>
    
    @if($orders->isEmpty())
        <div class="alert alert-info">
            Bạn chưa có đơn đặt bàn nào.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Số người</th>
                        <th>Trạng thái</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->booking_date->format('d/m/Y H:i') }}</td>
                        <td>{{ $order->number_of_people }}</td>
                        <td>
                            <span class="badge bg-{{ $order->status_color }}">
                                {{ $order->status_text }}
                            </span>
                        </td>
                        <td>{{ $order->special_request }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection 