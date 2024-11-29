@extends('layouts.front')

@section('title', 'Đặt bàn')

@section('hero-title', 'Đặt bàn')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('front.home') }}">Trang chủ</a></li>
<li class="breadcrumb-item"><a href="#">Trang</a></li>
<li class="breadcrumb-item text-white active" aria-current="page">Đặt bàn</li>
@endsection

@section('content')
<!-- Reservation Start -->
<div class="container-xxl py-5 px-0 wow fadeInUp" data-wow-delay="0.1s">
    <div class="row g-0">
        <div class="col-md-6">
            <div class="video">
                <button type="button" class="btn-play" data-bs-toggle="modal" data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-bs-target="#videoModal">
                    <span></span>
                </button>
            </div>
        </div>
        <div class="col-md-6 bg-dark d-flex align-items-center">
            <div class="p-5 wow fadeInUp" data-wow-delay="0.2s">
                <h5 class="section-title ff-secondary text-start text-primary fw-normal">Đặt bàn</h5>
                <h1 class="text-white mb-4">Đặt bàn trực tuyến</h1>
                <!-- Form đặt bàn -->
            </div>
        </div>
    </div>
</div>
<!-- Reservation End -->
@endsection