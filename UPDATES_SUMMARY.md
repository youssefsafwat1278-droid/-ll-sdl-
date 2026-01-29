# ملخص التحديثات - نظام Scout Tanzania

## تاريخ: 2026-01-21

---

## التحديثات المطبقة

### 1️⃣ نظام الملكية المحلي/الخارجي (Local/External Ownership)

#### ✅ ما تم تطبيقه:

**أ) الأعمدة في قاعدة البيانات:**
- ✅ `local_ownership_count` - عدد المالكين من نفس الرهط
- ✅ `external_ownership_count` - عدد المالكين من رهط أخرى

**ب) القيود:**
- **الكشافة العاديين:** 3 محلي + 5 خارجي
- **القادة/الرواد:** 20 إجمالي
- ✅ يتم تحديث العدادات تلقائياً عند كل اختيار/انتقال

**ج) التوفر (is_available):**
- الكشاف متاح طالما لم يمتلئ كلا الحدين
- يتم التحديث تلقائياً في `Scout::refreshAvailability()`

---

### 2️⃣ نظام Free Hit المحسّن

#### ✅ الميزات الجديدة:

**أ) اختيار اللاعبين المقفولين:**
- ✅ أثناء Free Hit: يمكن اختيار **أي لاعب** حتى المقفولين
- ✅ يتم التحقق من `can_pick` بدلاً من `is_available`

**ب) حفظ الفريق الأصلي:**
- ✅ يتم حفظ الفريق تلقائياً عند تفعيل Free Hit في `free_hit_snapshots`
- ✅ يحفظ جميع اللاعبين + المراكز + القائد/النائب

**ج) إرجاع الفريق تلقائياً:**
- ✅ Command جديد: `php artisan freehit:restore`
- ✅ يرجع الفريق للحالة الأصلية بعد انتهاء الجولة

**د) عدم تأثير Ownership:**
- ✅ التبديلات أثناء Free Hit لا تؤثر على `ownership_counts`
- ✅ لا توجد penalty على عدد التبديلات

---

### 3️⃣ التحديثات في الواجهات (Views)

#### ✅ صفحة التبديلات (transfers.blade.php):
- ✅ عرض `local/external ownership` للكشافة العاديين
- ✅ عرض `ownership/20` للقادة
- ✅ مؤشر Free Hit مع رسالة توضيحية
- ✅ عرض "✓ متاح" أو "✗ غير متاح" لكل كشاف
- ✅ التكلفة = 0 أثناء Free Hit

#### ✅ صفحة الكشافة (scouts/index.blade.php):
- ✅ Progress bars منفصلة للمحلي/الخارجي
- ✅ عرض `محلي: X/3 | خارجي: Y/5` للكشافة
- ✅ عرض `X/20` للقادة

#### ✅ صفحة فريقي (my-team.blade.php):
- ✅ تنبيه Free Hit في أعلى الصفحة
- ✅ رسالة توضح أن الفريق مؤقت

---

## الملفات المعدلة

### Backend:
```
✅ app/Models/Scout.php (تحديث دوال ownership)
✅ app/Models/FreeHitSnapshot.php (Model جديد)
✅ app/Http/Controllers/ChipController.php (حفظ الفريق)
✅ app/Http/Controllers/TransferController.php (Free Hit logic)
✅ app/Console/Commands/RestoreFreeHitTeams.php (Command جديد)
```

### Database:
```
✅ migrations/2026_01_21_090311_create_free_hit_snapshots_table.php
```

### Frontend:
```
✅ resources/views/transfers.blade.php
✅ resources/views/scouts/index.blade.php
✅ resources/views/my-team.blade.php
```

---

## كيفية الاستخدام

### 1. تفعيل Free Hit:
```bash
المستخدم يضغط على زر Free Hit من واجهة الـ Chips
→ يتم حفظ الفريق الحالي تلقائياً
→ يمكن اختيار أي لاعب (حتى المقفولين)
→ لا توجد penalty على التبديلات
```

### 2. إرجاع الفرق بعد الجولة:
```bash
# يدوياً
php artisan freehit:restore

# أو تلقائياً (أضف في Kernel.php)
$schedule->command('freehit:restore')->dailyAt('00:00');
```

### 3. مراقبة Ownership:
- **في الواجهة:** يظهر للمستخدم local/external counts
- **في الكود:** يتم التحديث تلقائياً عبر `incrementOwnershipFor()` و `decrementOwnershipFor()`

---

## الاختبار

### ✅ اختبار نظام الملكية:
```
1. اختر كشاف من نفس الرهط → local_ownership_count++
2. اختر كشاف من رهط أخرى → external_ownership_count++
3. تأكد من is_available يتحدث بشكل صحيح
```

### ✅ اختبار Free Hit:
```
1. فعّل Free Hit
2. تأكد من حفظ الفريق في free_hit_snapshots
3. اختر لاعبين مقفولين (يجب أن يعمل)
4. شغّل: php artisan freehit:restore
5. تأكد من إرجاع الفريق الأصلي
```

---

## الإعدادات الافتراضية

| الإعداد | القيمة |
|---------|--------|
| Local ownership (scouts) | 3 |
| External ownership (scouts) | 5 |
| Leaders ownership | 20 |
| Free Hit uses | 1 مرة/موسم |
| Transfer penalty | 4 نقاط |

---

## الخطوات التالية (اختياري)

### 🔄 جدولة تلقائية:
أضف في `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('freehit:restore')
        ->dailyAt('00:00')
        ->withoutOverlapping();
}
```

### 📊 تحسينات مستقبلية:
- [ ] إضافة إحصائيات عن استخدام Free Hit
- [ ] تنبيهات للمستخدمين قبل نهاية جولة Free Hit
- [ ] تاريخ استخدام Free Hit في الملف الشخصي

---

## المشاكل المحتملة والحلول

### ❌ المشكلة: ownership_count لا يتحدث
**✅ الحل:** تأكد من استخدام `incrementOwnershipFor()` و `decrementOwnershipFor()` بدلاً من `increment()` و `decrement()`

### ❌ المشكلة: Free Hit لا يسمح باختيار اللاعبين المقفولين
**✅ الحل:** تأكد من استخدام `can_pick` في الواجهة بدلاً من `is_available`

### ❌ المشكلة: الفريق لا يرجع بعد Free Hit
**✅ الحل:** شغّل `php artisan freehit:restore` بعد نهاية كل جولة

---

## الدعم

إذا واجهت أي مشكلة:
1. تحقق من logs في `storage/logs/laravel.log`
2. تأكد من تشغيل جميع migrations
3. تأكد من تشغيل `php artisan config:clear`

تم بنجاح! ✅
