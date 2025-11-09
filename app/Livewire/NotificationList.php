<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;

class NotificationList extends Component
{
    public $notifications;
    public $unreadCount = 0;
    
    protected $listeners = ['notification-received' => 'loadNotifications'];
    
    public function mount()
    {
        $this->loadNotifications();
    }
    
    public function loadNotifications()
    {
        $this->notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $this->unreadCount = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    }
    
    public function markAsRead($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->markAsRead();
        
        $this->loadNotifications();
        $this->dispatch('notification-read');
    }
    
    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        $this->loadNotifications();
        $this->dispatch('all-notifications-read');
    }
    
    public function render()
    {
        return view('livewire.notification-list');
    }
}