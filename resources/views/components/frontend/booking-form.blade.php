@props(['hotels' => null, 'compact' => false])

<form
    method="GET"
    action="{{ route('frontend.hotels') }}"
    class="rounded-2xl border border-[var(--border-light)] bg-[var(--surface-elevated)] p-6 shadow-xl"
>
    @if($compact)
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="check_in" class="form-label">
                    {{ __('auth.check_in') }}
                </label>
                <input
                    type="date"
                    id="check_in"
                    name="check_in"
                    min="{{ date('Y-m-d') }}"
                    value="{{ request('check_in') }}"
                    class="input"
                />
            </div>
            <div class="flex-1">
                <label for="check_out" class="form-label">
                    {{ __('auth.check_out') }}
                </label>
                <input
                    type="date"
                    id="check_out"
                    name="check_out"
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    value="{{ request('check_out') }}"
                    class="input"
                />
            </div>
            <button
                type="submit"
                class="btn-primary"
            >
                <i class="bi bi-search"></i>
                {{ __('auth.search') }}
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @if($hotels)
                <div class="{{ $hotels ? 'sm:col-span-2 lg:col-span-1' : '' }}">
                    <label for="hotel_id" class="form-label">
                        {{ __('auth.hotel') }}
                    </label>
                    <select
                        id="hotel_id"
                        name="hotel_id"
                        class="select"
                    >
                        <option value="">{{ __('auth.all_hotels') }}</option>
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ request('hotel_id') == $hotel->id ? 'selected' : '' }}>
                                {{ $hotel->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label for="check_in" class="form-label">
                    {{ __('auth.check_in') }}
                </label>
                <input
                    type="date"
                    id="check_in"
                    name="check_in"
                    min="{{ date('Y-m-d') }}"
                    value="{{ request('check_in') }}"
                    class="input"
                />
            </div>

            <div>
                <label for="check_out" class="form-label">
                    {{ __('auth.check_out') }}
                </label>
                <input
                    type="date"
                    id="check_out"
                    name="check_out"
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    value="{{ request('check_out') }}"
                    class="input"
                />
            </div>

            <div>
                <label for="guests" class="form-label">
                    {{ __('auth.guests') }}
                </label>
                <select
                    id="guests"
                    name="guests"
                    class="select"
                >
                    @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ request('guests', 1) == $i ? 'selected' : '' }}>
                            {{ $i }} {{ $i === 1 ? __('auth.guest') : __('auth.guests') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex items-end">
                <button
                    type="submit"
                    class="btn-primary w-full"
                >
                    <i class="bi bi-search"></i>
                    {{ __('auth.search') }}
                </button>
            </div>
        </div>
    @endif
</form>
