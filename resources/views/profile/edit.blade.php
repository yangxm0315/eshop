<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('个人中心') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- 成功提示 -->
            @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <!-- 用户资料 -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- 修改密码 -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- 收货地址管理 -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-semibold mb-4">收货地址</h3>

                <!-- 添加地址按钮 -->
                <button onclick="showAddressModal()" class="mb-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                    + 添加新地址
                </button>

                <!-- 地址列表 -->
                <div class="space-y-4">
                    @forelse($addresses as $address)
                    <div class="border rounded-lg p-4 {{ $address->is_default ? 'border-indigo-500 bg-indigo-50' : '' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium">
                                    {{ $address->name }}
                                    <span class="text-gray-500 font-normal ml-2">{{ $address->phone }}</span>
                                    @if($address->is_default)
                                    <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-600 text-xs rounded">默认</span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 mt-2">{{ $address->full_address }}</p>
                            </div>
                            <div class="flex space-x-2">
                                @if(!$address->is_default)
                                <form action="{{ route('profile.address.default', $address) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800">设为默认</button>
                                </form>
                                @endif
                                <button onclick="editAddress({{ $address->id }}, '{{ $address->name }}', '{{ $address->phone }}', '{{ $address->province }}', '{{ $address->city }}', '{{ $address->district }}', '{{ $address->detail }}', {{ $address->is_default ? 'true' : 'false' }})" class="text-sm text-indigo-600 hover:text-indigo-800">编辑</button>
                                <form action="{{ route('profile.address.delete', $address) }}" method="POST" class="inline" onsubmit="return confirm('确定要删除这个地址吗？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">删除</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-8">暂无收货地址</p>
                    @endforelse
                </div>
            </div>

            <!-- 删除账号 -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- 添加/编辑地址模态框 -->
<div id="addressModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
        <h3 id="modalTitle" class="text-lg font-bold mb-4">添加收货地址</h3>
        <form id="addressForm" method="POST">
            @csrf
            <input type="hidden" id="addressId" name="_method" value="POST">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">收货人</label>
                    <input type="text" name="name" id="addressName" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">联系电话</label>
                    <input type="text" name="phone" id="addressPhone" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">省份</label>
                    <input type="text" name="province" id="addressProvince" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">城市</label>
                    <input type="text" name="city" id="addressCity" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">区县</label>
                    <input type="text" name="district" id="addressDistrict" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">详细地址</label>
                <textarea name="detail" id="addressDetail" rows="3" required
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
            </div>

            <div class="mt-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_default" id="addressDefault" value="1" class="text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">设为默认地址</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="hideAddressModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">取消</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">保存</button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddressModal() {
    document.getElementById('modalTitle').textContent = '添加收货地址';
    document.getElementById('addressForm').action = "{{ route('profile.address.add') }}";
    document.getElementById('addressId').value = 'POST';
    document.getElementById('addressForm').reset();
    document.getElementById('addressModal').classList.remove('hidden');
}

function hideAddressModal() {
    document.getElementById('addressModal').classList.add('hidden');
}

function editAddress(id, name, phone, province, city, district, detail, isDefault) {
    document.getElementById('modalTitle').textContent = '编辑收货地址';
    document.getElementById('addressForm').action = "{{ url('profile/address') }}/" + id;
    document.getElementById('addressId').value = 'PUT';
    document.getElementById('addressName').value = name;
    document.getElementById('addressPhone').value = phone;
    document.getElementById('addressProvince').value = province;
    document.getElementById('addressCity').value = city;
    document.getElementById('addressDistrict').value = district;
    document.getElementById('addressDetail').value = detail;
    document.getElementById('addressDefault').checked = isDefault;
    document.getElementById('addressModal').classList.remove('hidden');
}
</script>
