<div>
    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Thiết bị *</label>
    <select name="equipment_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
        <option value="">-- Chọn thiết bị --</option>
        @foreach($equipment as $item)
            <option value="{{ $item->id }}">
                {{ $item->name }} (tồn {{ $item->stock_quantity }} {{ $item->unit }})
            </option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Phòng *</label>
    <select name="room_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
        <option value="">-- Chọn phòng --</option>
        @foreach($rooms as $room)
            <option value="{{ $room->id }}">P.{{ $room->room_number }} - {{ $room->building->name ?? 'N/A' }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Số lượng *</label>
    <input name="quantity" data-number required maxlength="7" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500" placeholder="{{ $mode === 'allocate' ? 'Số lượng bàn giao' : 'Số lượng thu hồi' }}">
    <span class="field-error hidden text-xs text-rose-400 mt-1"></span>
</div>
