# تقرير المراجعة الأمنية — ReserHotel

> **النطاق:** تطبيق ويب Laravel 12 (Blade + Alpine + Tailwind v4) لحجز الفنادق.
> **المنهجية:** OWASP Web Security Testing Guide + OWASP Top 10.
> **الافتراض:** صاحب المشروع أو مخوّل بالاختبار. **مراجعة ثابتة (SAST) يدوية على الكود المصدري** — لم تُنفَّذ اختبارات ديناميكية على خادم حيّ.

---

## 1) Executive Summary

التطبيق مبني على Laravel 12 بأسلوب آمن نسبيًا في النقاط الحرجة:

- **لا توجد SQL Injection مؤكدة** — جميع الاستعلامات عبر Eloquent/Query Builder مع bindings، والاستعلامات `raw` تستخدم ثوابت فقط (لا مدخلات مستخدم).
- **الحماية من CSRF مفعّلة** في الويب، وSanctum tokens للـ API.
- **كلمات المرور** مشفرة (cast `hashed`)، وتوجد rate limits للتسجيل/الدخول.
- **المصادقة/التفويض في معظم النقاط سليم** (idempotent ownership checks في الحجوزات والمراجعات والمدفوعات).

غير أن المراجعة رصدت **ثغرات وثغرات محتملة**، أبرزها:

| الأولوية | المشكلة |
|---|---|
| **عالية** | `APP_DEBUG=true` في الإنتاج = كشف تفاصيل تقنية/بيانات حساسة |
| **عالية (محتملة)** | فجوة تفويض: دور "owner" يملك صلاحيات إدارية كاملة غير مقصورة على منشآته + إمكانية إنشاء مستخدمين بدور `admin` |
| **متوسطة** | Stored XSS عبر حقل `icon` في Amenity (إخراج `{!! !!}` دون تحقق) |
| **متوسطة** | مراجعات لحجوزات لا يملكها المستخدم (IDOR-خفيف/منطق أعمال) |
| **متوسطة** | عدم التحقق من تطابق `hotel_id` مع `room_id` عند الحجز (سلامة بيانات) |
| **متوسطة** | غياب رؤوس أمان (Security Headers) → خطر Clickjacking |
| **منخفضة/متوسطة** | كوكيز الجلسة غير `Secure`، قالب بريد غير آمن خامل، تحسينات Mass Assignment |

**الخلاصة:** لا توجد ثغرات "Critical" مؤكدة في الكود (مثل SQLi أو RCE). المشروع جاهز للإصلاح بترتيب أولويات محدّد أدناه.

---

## 2) تحليل بنية المشروع

- **Framework:** Laravel 12 — PHP 8.2+.
- **الحزم الأمنية:** Laravel Fortify (مصادقة ويب)، Laravel Sanctum (API tokens)، spatie/laravel-permission (أدوار/صلاحيات)، Laravel Horizon (غير مكوّن → لا يظهر).
- **التوثيق:**
  - ويب: Fortify + `verified` + `auth` middleware + throttle محدد في `AppServiceProvider`.
  - API (`/api/v1`): Sanctum `auth:sanctum` + throttle عام `api` (120/دقيقة).
- **سطح الهجوم (Routes):**
  - عام (ويب): `/`, `/hotels`, `/search*`, `/gallery`, `/contact`, `/language/*`.
  - مسجّل (ويب): `/book/*`, `/my-dashboard/*`, `/notifications/*`.
  - إداري (`admin` middleware): CRUD كامل + رفع صور + إدارة مستخدمين.
  - API عام: `/register`, `/login`, `/hotels`, `/rooms/available`.
  - API خاص: bookings/payments/reviews.
- **الوصول الإداري:** `EnsureUserIsAdmin` يسمح بـ `isAdmin() || isOwner()` — بدون حدود على مستوى الكائن.

---

## 3) قائمة الثغرات المفصّلة

### 3.1 كشف بيانات حساسة عبر APP_DEBUG (إعداد خاطئ)
- **الموقع:** `.env` — `APP_DEBUG=true`، `APP_ENV=local`.
- **التصنيف:** Security Misconfiguration / Sensitive Data Exposure (OWASP A05/A02).
- **الخطورة:** **High** (إن تم الإطلاق هكذا).
- **السبب:** في بيئة الإنتاج تُظهر صفحات الخطأ (Ignition) stack traces كاملة: مسارات، اتصالات DB، مفاتيح، قيم session — تتيح للمهاجم جمع معلومات أو تسريب بيانات.
- **التحقق:** فتح أي مسار خاطئ في production وإظهار تفاصيل الاستثناء.

**قبل الإصلاح:**
```
APP_ENV=local
APP_DEBUG=true
```
**بعد الإصلاح (في ملف .env الخاص بالإنتاج):**
```
APP_ENV=production
APP_DEBUG=false
```
إضافة فحص احترازي في `bootstrap/app.php`:
```php
->withExceptions(function (Exceptions $exceptions) {
    if (app()->isProduction()) {
        $exceptions->shouldRenderJsonWhen(fn () => true);
    }
})
```

---

### 3.2 فجوة تفويض لدور "Owner" (احتمالية Privilege Escalation)
- **الموقع:**
  - `app/Http/Middleware/EnsureUserIsAdmin.php:13` — يسمح لـ admin **أو** owner بكل لوحة الإدارة.
  - `app/Http/Controllers/Admin/UserController.php:55-62` — `StoreUserRequest` يسمح بإنشاء مستخدم بدور `admin` (الملف `app/Http/Requests/Admin/StoreUserRequest.php:21`).
  - كل CRUD الإداري لا يُقيّد بـ `user_id` (مثلاً `HotelController::store` يضع `user_id = auth()->id()` لكن index/update/destroy يصلان لأي سجل).
- **التصنيف:** Broken Access Control (OWASP A01).
- **الخطورة:** **High (محتملة)** — تعتمد على نموذج الأعمال: إذا كان "owner" دورًا أدنى من admin، فيمكنه الوصول لكل الفنادق/الحجوزات وإنشاء admin جديد.
- **التحقق المطلوب:** تأكيد النية من صاحب المشروع حول أدوار owner؛ ثم محاولة owner الوصول لسجلات مستخدم آخر.

**قبل الإصلاح (EnsureUserIsAdmin):**
```php
abort_unless($request->user() && ($request->user()->isAdmin() || $request->user()->isOwner()), 403);
```
**بعد الإصلاح (مثال — قصر owner على فندقه):**
```php
public function handle(Request $request, Closure $next, string $scope = 'admin'): Response
{
    $user = $request->user();
    if (! $user || ! ($user->isAdmin() || $user->isOwner())) {
        abort(403);
    }
    if ($scope === 'owner' && ! $user->isAdmin()) {
        $request->attributes->set('owner_scope', $user->id);
    }
    return $next($request);
}
```
وأيضًا: منع إنشاء `admin` إلا من admin فعلي:
```php
// في UserController::store
if (in_array($data['role'], ['admin'], true) && ! auth()->user()->isAdmin()) {
    abort(403);
}
```

---

### 3.3 Stored XSS عبر حقل Amenity icon
- **الموقع:**
  - الإدخال: `app/Http/Requests/Admin/StoreAmenityRequest.php:19` — `'icon' => ['nullable','string','max:255']` بلا قائمة بيضاء.
  - الإخراج الخام: `resources/views/components/frontend/amenity-badge.blade.php:13` و`components/frontend/room-card.blade.php:91` و`frontend/hotel-show.blade.php:90` — `{!! $amenity->icon !!}`.
- **التصنيف:** Injection → XSS مخزّن (OWASP A03).
- **الخطورة:** **Medium** (يتطلب حساب admin/owner، لكن الأثر على كل زوار الصفحات العامة).
- **السبب:** قيمة `icon` تُدخل كـ HTML خام دون هروب/قائمة بيضاء؛ يمكن لصاحب حساب مخترق (owner) أو مسؤول إدخال `<script>` أو سمات أحداث تُنفَّذ في متصفح كل زائر.

**قبل الإصلاح:**
```php
'icon' => ['nullable', 'string', 'max:255'],
```
```blade
<span class="text-primary">{!! $amenity->icon !!}</span>
```
**بعد الإصلاح (قائمة بيضاء + هروب):**
```php
'icon' => ['nullable', 'string', 'max:255', Rule::notIn(['<script>', ...])], // ضعيف
// الأفضل: قائمة بيضاء بالأنماط المسموحة فقط:
'icon' => ['nullable', 'string', 'max:255', 'regex:/^bi\s+bi-[a-z0-9-]+$/'],
```
```blade
<span class="text-primary"><i class="{{ $amenity->icon }}"></i></span>
```

---

### 3.4 مراجعة لحجز لا يملكه المستخدم (IDOR/منطق أعمال)
- **الموقع:** `app/Http/Requests/StoreReviewRequest.php:20-25` و`app/Http/Requests/Api/V1/StoreReviewRequest.php:19` — `reservation_id` يخضع فقط لـ `exists:reservations,id` + `unique`. والـ API (`Api/V1/ReviewController::store:56`) يأخذ `$request->reservation_id` مباشرة.
- **التصنيف:** Broken Access Control / IDOR (OWASP A01).
- **الخطورة:** **Medium**.
- **السبب:** لا يوجد تحقق أن `reservation.user_id === auth()->id()`، والحقل اختياري (nullable)؛ أي مستخدم يكتب مراجعة وتقييمًا لأي فندق دون إقامة حقيقية، أو يربط مراجعته بحجز شخص آخر → مراجعات وهمية وتلاعب بالتقييمات.

**بعد الإصلاح (التحقق من الملكية):**
```php
public function withValidator($validator): void
{
    $validator->after(function ($validator) {
        if ($this->input('reservation_id')) {
            $owned = Reservation::where('id', $this->input('reservation_id'))
                ->where('user_id', $this->user()->id)
                ->whereIn('status', ['completed', 'checked_out', 'confirmed'])
                ->exists();
            if (! $owned) {
                $validator->errors()->add('reservation_id', __('auth.review_reservation_required'));
            }
        }
    });
}
```

---

### 3.5 عدم تطابق hotel_id مع room_id عند الحجز (سلامة البيانات)
- **الموقع:**
  - `app/Http/Requests/StoreBookingRequest.php:28-29` (ويب) و`app/Http/Requests/Api/V1/StoreBookingRequest.php:20-21`.
  - الاستخدام: `app/Services/BookingService.php:111-122` و`Api/V1/BookingController.php:67-78`.
- **التصنيف:** Business Logic / Broken Access Control.
- **الخطورة:** **Medium**.
- **السبب:** يُقبل `hotel_id` و`room_id` كقيم مستقلّة؛ لا يوجد فحص `room.hotel_id === hotel_id`. يمكن إنشاء حجز بفندق مختلف عن غرفته، ما يفسد الفواتير/الإشعارات/التقارير، وقد يؤثر على حساب التوفر.

**بعد الإصلاح (إضافة فحص مشترك):**
```php
$validator->after(function ($validator) {
    $room = Room::find($this->input('room_id'));
    if ($room && (int) $room->hotel_id !== (int) $this->input('hotel_id')) {
        $validator->errors()->add('room_id', __('validation-custom.room_hotel_mismatch'));
    }
});
```

---

### 3.6 غياب رؤوس الأمان → Clickjacking
- **الموقع:** `bootstrap/app.php` — لا توجد إضافة لرؤوس أمان، ولا middleware خاص بها.
- **التصنيف:** Security Misconfiguration (OWASP A05).
- **الخطورة:** **Medium**.
- **السبب:** غياب `X-Frame-Options`/`frame-ancestors` يتيح تضمين الموقع في iframe وتنفيذ Clickjacking على عمليات الحجز/الإدارة.

**بعد الإصلاح (مثال في `bootstrap/app.php`):**
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [App\Http\Middleware\SetLocale::class]);
    $middleware->web(prepend: [
        Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        // أو middleware مخصص يضيف:
        // X-Frame-Options: DENY
        // X-Content-Type-Options: nosniff
        // Referrer-Policy: strict-origin-when-cross-origin
        // Content-Security-Policy: ...
        // Permissions-Policy: ...
    ]);
})
```

---

### 3.7 كوكيز الجلسة غير Secure (HTTP)
- **الموقع:** `config/session.php` — `'secure' => env('SESSION_SECURE_COOKIE')` والقيمة غير مضبوطة في `.env` → false.
- **التصنيف:** Sensitive Data Exposure / Session Management.
- **الخطورة:** **Low–Medium** (على HTTPS فقط).
- **السبب:** السماح بإرسال كوكيز الجلسة عبر HTTP يعرّض التوكن للاعتراض على شبكة غير موثوقة.

**بعد الإصلاح في `.env`:**
```
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

---

### 3.8 قالب بريد HTML غير آمن (خامل — خطر كامن)
- **الموقع:** `resources/views/emails/notification.blade.php:48` — `{!! $line !!}`.
- **التصنيف:** Injection → HTML Injection في البريد.
- **الخطورة:** **Low** (حاليًا القالب غير مستدعى؛ إشعارات `MailMessage` تستخدم قالب Laravel الافتراضي الذي يهرب النصوص).
- **السبب:** أي `line()` بمحتوى من المستخدم (مثل تعليق المراجعة) سينفَّذ كـ HTML داخل البريد لو تم ربط هذا القالب.
- **التحقق:** البحث عن `->view('emails.notification')` أو `new HtmlString(...)` — غير موجود حاليًا.

**بعد الإصلاح:**
```blade
<p class="text">{{ $line }}</p>
```

---

### 3.9 ملاحظات Mass Assignment (تحصين)
- **الموقع:** `app/Models/Review.php:65-74` يتضمن `is_approved` ضمن `$fillable`؛ و`Hotel.php:88-103` يتضمن `user_id`.
- **التصنيف:** Security hardening.
- **الخطورة:** **Low** — غير قابلة للاستغلال حاليًا (المتحكمات تضبط `is_approved => false` يدويًا و`user_id` من `auth()->id()`).
- **التحسين:** حذف `is_approved` من `$fillable` والسماح بتغييرها فقط عبر `approve()/reject()`.

---

## 4) فئات تم فحصها ولم تُرصد بها ثغرات مؤكدة

- **SQL Injection:** لا توجد مدخلات مستخدم داخل `raw`؛ كلها ثوابت (تحقق في `SearchController`, `DashboardController`, `FrontendController`, `AvailabilityService`). المتابعة عبر `like` مهروبة bindings.
- **CSRF:** مفعّل افتراضيًا على كل POST/PUT/DELETE (ويب) + Sanctum (API).
- **Command Injection / SSRF / XXE:** لا توجد دوال `exec/shell_exec/system/passthru` أو جلب URLs من المستخدم أو `simplexml` من مدخلات.
- **Path Traversal:** أسماء ملفات مولّدة عشوائيًا بواسطة `store()`، والحذف عبر مسارات محفوظة بقاعدة البيانات.
- **File Upload:** قواعد `image` + `mimes:jpg,jpeg,png,webp` + `max:2048` مع تحقق من المحتوى الفعلي (rule `image`).
- **Insecure Deserialization:** لا `unserialize` من إدخالات.
- **Session Management:** دوران الجلسة عند الدخول (Laravel)، `http_only=true`.
- **مصادقة API:** تسجيل بدون تحقق بريد (مقبول لو كان السلوك مقصودًا) + rate limits.

---

## 5) ممارسات أمنية مفقودة (توصيات عامة)

1. **رؤوس الأمان:** CSP، `X-Frame-Options`، `X-Content-Type-Options`، `Referrer-Policy`، `Permissions-Policy`.
2. **HTTPS إلزامي** + HSTS + `SESSION_SECURE_COOKIE=true`.
3. **قوائم بيضاء** بدل القوائم السوداء لحقول مثل `icon` و`slug`.
4. **سياسة مراجعة صارمة** (ownership) على `reservation_id` في المراجعات.
5. **استخدام `Password::defaults()`** في `StoreUserRequest`/`UpdateUserRequest` (حاليًا `string|min:8` فقط).
6. **فصل أدوار owner/admin** وتطبيق middleware `permission` الموجود وغير المستخدم حاليًا (`EnsureUserHasPermission`).
7. **التحديث الدوري** للحزم عبر `composer audit` و`npm audit`.
8. **تشفير الجلسة:** `SESSION_ENCRYPT=true` اختياريًا.
9. **التحقق من الرموز:** النظر في مدة صلاحية Sanctum tokens (`expiration`).
10. **مراجعة دورية:** إعادة الفحص بعد كل تغيير + اختبارات ديناميكية (DAST) على بيئة اختبار.

---

## 6) تقييم المخاطر والترتيب المقترح للإصلاح

| # | الثغرة | الخطورة | الأولوية |
|---|--------|---------|----------|
| 1 | `APP_DEBUG=true` في الإنتاج | High | **فورية** |
| 2 | تفويض owner الشامل + إنشاء admin | High (محتملة) | **عالية** |
| 3 | Stored XSS في Amenity icon | Medium | عالية |
| 4 | مراجعات لحجوزات غير مملوكة | Medium | عالية |
| 5 | عدم تطابق hotel_id/room_id | Medium | متوسطة |
| 6 | رؤوس أمان مفقودة / Clickjacking | Medium | متوسطة |
| 7 | كوكيز الجلسة غير Secure | Low–Med | متوسطة |
| 8 | قالب بريد غير آمن (خامل) | Low | منخفضة |
| 9 | Mass Assignment (تحصين) | Low | منخفضة |

---

## 7) الملحق: أدوات ووثائق مقترحة

- `composer audit` / `npm audit` — فحص تبعيات.
- PHPStan (مثبّت) + Pint — جودة وثبات الكود.
- Ladislas/laravel-security-checker أو GitHub Dependabot.
- اختبارات PHPUnit لأمنية: CSRF، إذونات، IDOR، صلاحيات الملفات.
