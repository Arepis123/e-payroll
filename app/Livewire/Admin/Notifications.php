<?php

namespace App\Livewire\Admin;

use App\Models\NotificationTemplate;
use App\Models\NotificationLog;
use App\Models\User;
use App\Services\NotificationService;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;

class Notifications extends Component
{
    use WithFileUploads;

    #[Url]
    public $activeTab = 'templates';

    public $showTemplateModal = false;
    public $editingTemplateId = null;
    public $templateForm = [
        'name' => '',
        'slug' => '',
        'type' => 'email',
        'trigger_type' => 'manual',
        'trigger_days_before' => null,
        'subject' => '',
        'body' => '',
        'is_active' => true,
    ];

    public $selectedTemplateId = null;
    public $selectedRecipients = [];
    public $customBody = '';
    public $sendToAll = false;
    public $attachments = null;

    protected $notificationService;

    public function boot(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function openTemplateModal($templateId = null)
    {
        if ($templateId) {
            $template = NotificationTemplate::findOrFail($templateId);
            $this->editingTemplateId = $template->id;
            $this->templateForm = [
                'name' => $template->name,
                'slug' => $template->slug,
                'type' => $template->type,
                'trigger_type' => $template->trigger_type,
                'trigger_days_before' => $template->trigger_days_before,
                'subject' => $template->subject,
                'body' => $template->body,
                'is_active' => $template->is_active,
            ];
        } else {
            $this->resetTemplateForm();
        }
        $this->showTemplateModal = true;
    }

    public function closeTemplateModal()
    {
        $this->showTemplateModal = false;
        $this->resetTemplateForm();
    }

    public function resetTemplateForm()
    {
        $this->editingTemplateId = null;
        $this->templateForm = [
            'name' => '',
            'slug' => '',
            'type' => 'email',
            'trigger_type' => 'manual',
            'trigger_days_before' => null,
            'subject' => '',
            'body' => '',
            'is_active' => true,
        ];
    }

    public function saveTemplate()
    {
        $validated = $this->validate([
            'templateForm.name' => 'required|string|max:255',
            'templateForm.slug' => 'required|string|max:255|unique:notification_templates,slug,' . $this->editingTemplateId,
            'templateForm.type' => 'required|in:email,sms,system',
            'templateForm.trigger_type' => 'required',
            'templateForm.subject' => 'required_if:templateForm.type,email',
            'templateForm.body' => 'required|string',
        ]);

        // Prepare data for saving
        $templateData = $this->templateForm;

        // Set variables as null or empty array to avoid issues
        $templateData['variables'] = null;

        if ($this->editingTemplateId) {
            $template = NotificationTemplate::findOrFail($this->editingTemplateId);
            $template->update($templateData);
            session()->flash('success', 'Template updated successfully!');
        } else {
            NotificationTemplate::create($templateData);
            session()->flash('success', 'Template created successfully!');
        }

        $this->closeTemplateModal();
    }

    public function deleteTemplate($templateId)
    {
        NotificationTemplate::findOrFail($templateId)->delete();
        session()->flash('success', 'Template deleted successfully!');
    }

    public function toggleTemplateStatus($templateId)
    {
        $template = NotificationTemplate::findOrFail($templateId);
        $template->update(['is_active' => !$template->is_active]);
    }

    public function removeAttachment()
    {
        $this->attachments = null;
    }

    public function sendNotification()
    {
        // Validate inputs
        $this->validate([
            'selectedTemplateId' => 'required',
            'selectedRecipients' => 'required_unless:sendToAll,true',
            'attachments' => 'nullable|file|max:1024', // 1MB max
        ]);

        $template = NotificationTemplate::findOrFail($this->selectedTemplateId);

        $recipients = $this->sendToAll
            ? User::where('role', 'client')->get()
            : User::whereIn('id', $this->selectedRecipients)->get();

        // Handle file upload
        $uploadedFiles = [];
        if ($this->attachments) {
            $path = $this->attachments->store('notifications/attachments', 'local');
            $uploadedFiles[] = $path;
        }

        foreach ($recipients as $recipient) {
            $this->notificationService->sendFromTemplate(
                $template,
                $recipient,
                ['client_name' => $recipient->name],
                null,
                null,
                null,
                $uploadedFiles,
                $this->customBody
            );
        }

        session()->flash('success', 'Notifications sent to ' . $recipients->count() . ' recipient(s)!');
        $this->reset(['selectedTemplateId', 'selectedRecipients', 'customBody', 'sendToAll', 'attachments']);
    }

    public function render()
    {
        $templates = NotificationTemplate::orderBy('created_at', 'desc')->get();
        $allLogs = NotificationLog::with(['recipient', 'sender', 'template'])
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        // Group logs by batch (same template, similar time, same sender)
        $groupedLogs = [];
        foreach ($allLogs as $log) {
            $key = $log->notification_template_id . '_' . $log->sent_by . '_' . $log->created_at->format('Y-m-d H:i');
            if (!isset($groupedLogs[$key])) {
                $groupedLogs[$key] = [
                    'primary' => $log,
                    'recipients' => [],
                ];
            }
            $groupedLogs[$key]['recipients'][] = $log;
        }

        // Convert to collection and limit to 50 batches
        $logs = collect($groupedLogs)->values()->take(50);

        $clients = User::where('role', 'client')->orderBy('name')->get();
        $stats = [
            'total_templates' => NotificationTemplate::count(),
            'active_templates' => NotificationTemplate::where('is_active', true)->count(),
            'sent_today' => NotificationLog::whereDate('created_at', today())->where('status', 'sent')->count(),
            'failed_today' => NotificationLog::whereDate('created_at', today())->where('status', 'failed')->count(),
        ];

        return view('livewire.admin.notifications', compact('templates', 'logs', 'clients', 'stats'))
            ->layout('components.layouts.app', ['title' => __('Notifications & Reminders')]);
    }
}
