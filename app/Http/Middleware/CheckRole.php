<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect('login');
        }

        // Lấy role của user hiện tại
        $userRole = Auth::user()->role;

        // Kiểm tra role
        if ($role == 'admin' && $userRole != 1) {
            return redirect('/'); // Chuyển về trang chủ nếu không phải admin
        }

        if ($role == 'user' && $userRole != 0) {
            return redirect('/admin/dashboard'); // Chuyển về dashboard nếu là admin
        }

        return $next($request);
    }
} 