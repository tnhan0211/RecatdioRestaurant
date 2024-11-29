@extends('layouts.front')

@section('title', 'Đánh giá từ khách hàng')

@section('hero-title', 'Đánh giá')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('front.home') }}">Trang chủ</a></li>
<li class="breadcrumb-item"><a href="#">Trang</a></li>
<li class="breadcrumb-item text-white active" aria-current="page">Đánh giá</li>
@endsection

@section('content')
<!-- Testimonial Start -->
<div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container">
        <div class="text-center">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Đánh giá</h5>
            <h1 class="mb-5">Khách hàng nói gì!!!</h1>
        </div>
        <!-- Nội dung phần testimonial -->
    </div>
</div>
<!-- Testimonial End -->
@endsection