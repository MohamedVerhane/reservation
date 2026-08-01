<x-layouts.admin :title="__('admin.reservations.edit_title')" active="reservations">

    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.reservations.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.reservations') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ __('admin.reservations.edit_title_with_id', ['id' => $reservation->id]) }}</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 px-5 py-3">
            <p class="text-sm font-semibold text-red-700 dark:text-red-400 mb-2">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ __('admin.form.fix_errors') }}
            </p>
            <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-red-600 dark:text-red-400"></i>
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ session('error') }}</p>
        </div>
    @endif

    <div x-data="reservationForm()" x-init="init()">

        <form method="POST" action="{{ route('admin.reservations.update', $reservation) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left Column --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Booking Details --}}
                    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 p-6">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
                            <i class="bi bi-person-lines-fill me-1.5 text-indigo-500"></i> {{ __('admin.reservations.booking_details') }}
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="user_id" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.reservations.guest') }}</label>
                                <select name="user_id" id="user_id" required
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                                    <option value="">{{ __('admin.reservations.guest_select') }}</option>
                                    @foreach ($guests as $id => $name)
                                        <option value="{{ $id }}" {{ old('user_id', $reservation->user_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="hotel_id" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.reservations.hotel') }}</label>
                                <select name="hotel_id" id="hotel_id" required x-model="hotelId" x-on:change="loadRooms()"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                                    <option value="">{{ __('admin.reservations.hotel_select') }}</option>
                                    @foreach ($hotels as $id => $name)
                                        <option value="{{ $id }}" {{ old('hotel_id', $reservation->hotel_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('hotel_id')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="room_id" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.reservations.room') }}</label>
                                <select name="room_id" id="room_id" required x-model="roomId" x-on:change="calculatePrice()"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                                    :disabled="roomsLoading"
                                    >
                                    <option value="">{{ __('admin.reservations.room_select') }}</option>
                                    <template x-for="room in rooms" :key="room.id">
                                        <option :value="room.id" x-text="room.label + ' - $' + parseFloat(room.base_price).toFixed(0) + '{{ __("admin.reservations.per_night") }}'"></option>
                                    </template>
                                </select>
                                <p x-show="roomsLoading" class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                                    <i class="bi bi-arrow-repeat animate-spin me-1"></i> {{ __('admin.reservations.loading_rooms') }}
                                </p>
                                @error('room_id')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="guests" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.reservations.number_of_guests') }}</label>
                                <input type="number" name="guests" id="guests" min="1" max="20" value="{{ old('guests', $reservation->guests) }}" required
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                                @error('guests')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="children_count" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.reservations.children') }}</label>
                                <input type="number" name="children_count" id="children_count" min="0" max="10" value="{{ old('children_count', $reservation->children_count) }}"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                                @error('children_count')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="notes" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.reservations.notes') }}</label>
                                <textarea name="notes" id="notes" rows="3" maxlength="1000"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all resize-none"
                                    placeholder="{{ __('admin.reservations.notes_placeholder') }}">{{ old('notes', $reservation->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Stay Dates --}}
                    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900 p-6">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
                            <i class="bi bi-calendar-range me-1.5 text-indigo-500"></i> {{ __('admin.reservations.stay_dates') }}
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="check_in" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.reservations.checkin_date') }}</label>
                                <input type="date" name="check_in" id="check_in" required x-model="checkIn" x-on:change="onDatesChanged()"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                                @error('check_in')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="check_out" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.reservations.checkout_date') }}</label>
                                <input type="date" name="check_out" id="check_out" required x-model="checkOut" x-on:change="onDatesChanged()"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                                @error('check_out')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="space-y-6">

                    {{-- Price Summary --}}
                    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-violet-50/60 dark:bg-slate-900 p-6 sticky top-6">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
                            <i class="bi bi-receipt me-1.5 text-indigo-500"></i> {{ __('admin.reservations.price_summary') }}
                        </h3>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500 dark:text-slate-400">{{ __('admin.reservations.rate_per_night') }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="perNight ? '$' + perNight : '—'"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500 dark:text-slate-400">{{ __('admin.reservations.number_of_nights') }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="nights > 0 ? nights : '—'"></span>
                            </div>
                            <div class="border-t border-slate-100 dark:border-slate-800 pt-3 flex items-center justify-between">
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.reservations.total_label') }}</span>
                                <span class="text-lg font-extrabold text-indigo-600 dark:text-indigo-400" x-text="totalPrice ? '$' + totalPrice : '—'"></span>
                            </div>
                        </div>

                        <p x-show="priceLoading" class="text-xs text-slate-400 dark:text-slate-500 text-center mb-4">
                            <i class="bi bi-arrow-repeat animate-spin me-1"></i> {{ __('admin.reservations.calculating') }}
                        </p>

                        <button type="submit"
                            class="w-full btn-gradient inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98]"
                            :disabled="submitting">
                            <i class="bi bi-check-lg text-base"></i> {{ __('admin.reservations.update_submit') }}
                        </button>

                        <a href="{{ route('admin.reservations.index') }}"
                            class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            {{ __('admin.reservations.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function reservationForm() {
            return {
                hotelId: '{{ old('hotel_id', $reservation->hotel_id) }}',
                roomId: '{{ old('room_id', $reservation->room_id) }}',
                checkIn: '{{ old('check_in', $reservation->check_in->format('Y-m-d')) }}',
                checkOut: '{{ old('check_out', $reservation->check_out->format('Y-m-d')) }}',
                rooms: [],
                roomsLoading: false,
                priceLoading: false,
                submitting: false,
                nights: 0,
                perNight: null,
                totalPrice: null,

                init() {
                    if (this.hotelId) {
                        this.loadRooms();
                    }
                    if (this.checkIn && this.checkOut) {
                        this.calculatePrice();
                    }
                },

                loadRooms() {
                    if (!this.hotelId) {
                        this.rooms = [];
                        this.roomId = '';
                        return;
                    }

                    this.roomsLoading = true;
                    this.rooms = [];
                    this.roomId = '';
                    this.resetPrice();

                    let url = '/admin/reservations/hotels/' + this.hotelId + '/rooms';
                    let params = [];
                    if (this.checkIn) params.push('check_in=' + this.checkIn);
                    if (this.checkOut) params.push('check_out=' + this.checkOut);
                    if (params.length) url += '?' + params.join('&');

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            this.rooms = data;
                            this.roomsLoading = false;
                        })
                        .catch(() => {
                            this.roomsLoading = false;
                        });
                },

                onDatesChanged() {
                    this.loadRooms();
                    if (this.roomId && this.checkIn && this.checkOut) {
                        this.calculatePrice();
                    }
                },

                calculatePrice() {
                    if (!this.roomId || !this.checkIn || !this.checkOut) {
                        this.resetPrice();
                        return;
                    }

                    this.priceLoading = true;

                    let url = '/admin/reservations/calculate-price?room_id=' + this.roomId + '&check_in=' + this.checkIn + '&check_out=' + this.checkOut;

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            this.nights = data.nights;
                            this.perNight = data.per_night;
                            this.totalPrice = data.total_price;
                            this.priceLoading = false;
                        })
                        .catch(() => {
                            this.resetPrice();
                            this.priceLoading = false;
                        });
                },

                resetPrice() {
                    this.nights = 0;
                    this.perNight = null;
                    this.totalPrice = null;
                }
            };
        }
    </script>
    @endpush

</x-layouts.admin>
