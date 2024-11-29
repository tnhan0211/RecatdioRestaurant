@extends('layouts.front')

@section('title', 'Đội ngũ của chúng tôi')

@section('hero-title', 'Đội ngũ')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('front.home') }}">Trang chủ</a></li>
<li class="breadcrumb-item"><a href="#">Trang</a></li>
<li class="breadcrumb-item text-white active" aria-current="page">Đội ngũ</li>
@endsection

@section('content')
<!-- Team Start -->
<div class="container-xxl pt-5 pb-3">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Thành viên</h5>
            <h1 class="mb-5">Đầu bếp của chúng tôi</h1>
        </div>
        <!-- Nội dung phần team -->
    </div>
</div>
<!-- Team End -->
@endsection