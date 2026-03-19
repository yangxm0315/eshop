<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * 显示用户个人中心
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();

        return view('profile.edit', [
            'user' => $user,
            'addresses' => $addresses,
        ]);
    }

    /**
     * 更新用户资料
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * 删除用户账号
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * 添加收货地址
     */
    public function addAddress(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'district' => 'required|string|max:50',
            'detail' => 'required|string|max:200',
            'is_default' => 'boolean',
        ]);

        // 如果设为默认，先取消其他默认地址
        if ($request->is_default) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        Address::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'detail' => $request->detail,
            'is_default' => $request->is_default ?? false,
        ]);

        return Redirect::route('profile.edit')->with('success', '地址添加成功');
    }

    /**
     * 更新收货地址
     */
    public function updateAddress(Request $request, Address $address): RedirectResponse
    {
        // 权限检查
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'district' => 'required|string|max:50',
            'detail' => 'required|string|max:200',
            'is_default' => 'boolean',
        ]);

        // 如果设为默认，先取消其他默认地址
        if ($request->is_default) {
            Address::where('user_id', $request->user()->id)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($request->only(['name', 'phone', 'province', 'city', 'district', 'detail', 'is_default']));

        return Redirect::route('profile.edit')->with('success', '地址更新成功');
    }

    /**
     * 删除收货地址
     */
    public function deleteAddress(Request $request, Address $address): RedirectResponse
    {
        // 权限检查
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $address->delete();

        return Redirect::route('profile.edit')->with('success', '地址删除成功');
    }

    /**
     * 设置默认地址
     */
    public function setDefaultAddress(Request $request, Address $address): RedirectResponse
    {
        // 权限检查
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return Redirect::route('profile.edit')->with('success', '默认地址已设置');
    }
}
