<?php
// app/Http/Controllers/Admin/NotificationController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminNotificationMail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('user')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $users = User::where('role', 'user')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.notifications.index', compact('notifications', 'users'));
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:price_alert,deadline,news,ranking,other',
            'title' => 'required|string|max:200',
            'message' => 'required|string',
            'target' => 'required|in:all,users',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'delivery' => 'required|in:in_app,email',
        ]);

        // تحديد المستخدمين المستهدفين
        $targetUsers = collect();
        if ($validated['target'] === 'users') {
            // التحقق من وجود user_ids
            if (empty($validated['user_ids'])) {
                return redirect()
                    ->route('admin.notifications.index')
                    ->with('error', 'اختر مستخدمين لإرسال الإشعار.');
            }
            $targetUsers = User::whereIn('id', $validated['user_ids'])->get();
            if ($targetUsers->isEmpty()) {
                return redirect()
                    ->route('admin.notifications.index')
                    ->with('error', 'المستخدمون المحددون غير موجودين.');
            }
        } else {
            $targetUsers = User::where('role', 'user')->get();
        }

        // إرسال داخل الصفحة
        if ($validated['delivery'] === 'in_app') {
            try {
                $now = now();
                $rows = $targetUsers->map(function ($user) use ($validated, $now) {
                    return [
                        'user_id' => $user->id,
                        'type' => $validated['type'],
                        'title' => $validated['title'],
                        'message' => $validated['message'],
                        'is_read' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })->all();

                if (!empty($rows)) {
                    Notification::insert($rows);
                }

                return redirect()
                    ->route('admin.notifications.index')
                    ->with('success', "تم إرسال الإشعار داخل الصفحة بنجاح إلى {$targetUsers->count()} مستخدم.");
            } catch (\Exception $e) {
                return redirect()
                    ->route('admin.notifications.index')
                    ->with('error', 'حدث خطأ أثناء إرسال الإشعار: ' . $e->getMessage());
            }
        }

        // إرسال إلى الإيميل
        try {
            $emailUsers = $targetUsers->filter(function ($user) {
                return !empty($user->email);
            });

            if ($emailUsers->isEmpty()) {
                return redirect()
                    ->route('admin.notifications.index')
                    ->with('error', 'لا يوجد بريد إلكتروني للمستخدمين المحددين.');
            }

            $mailNotification = new Notification([
                'type' => $validated['type'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'is_read' => false,
            ]);

            $sentCount = 0;
            foreach ($emailUsers as $user) {
                try {
                    Mail::to($user->email)->send(new AdminNotificationMail($mailNotification));
                    $sentCount++;
                } catch (\Exception $e) {
                    \Log::error("Failed to send email to {$user->email}: " . $e->getMessage());
                }
            }

            if ($sentCount === 0) {
                return redirect()
                    ->route('admin.notifications.index')
                    ->with('error', 'فشل إرسال الإيميل. تحقق من إعدادات البريد الإلكتروني.');
            }

            return redirect()
                ->route('admin.notifications.index')
                ->with('success', "تم إرسال الإشعار إلى {$sentCount} إيميل بنجاح.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.notifications.index')
                ->with('error', 'حدث خطأ أثناء إرسال الإيميل: ' . $e->getMessage());
        }
    }

}
