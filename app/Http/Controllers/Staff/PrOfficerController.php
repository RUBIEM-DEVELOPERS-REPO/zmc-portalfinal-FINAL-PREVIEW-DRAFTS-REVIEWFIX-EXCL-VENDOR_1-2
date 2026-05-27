<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Event;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrOfficerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff.portal', 'role:pr_officer']);
    }

    /**
     * Display the PR Officer dashboard with Notices, Events, and News.
     */
    public function dashboard()
    {
        $notices = Notice::orderByDesc('id')->paginate(15, ['*'], 'notices');
        $events = Event::orderByDesc('starts_at')->paginate(15, ['*'], 'events');
        $news = News::orderByDesc('published_at')->orderByDesc('id')->paginate(15, ['*'], 'news');

        return view('staff.pr.dashboard', compact('notices', 'events', 'news'));
    }

    /**
     * Store a new notice.
     */
    public function storeNotice(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'target_portal' => ['required', 'in:journalist,mediahouse,both'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? true);
        $data['published_at'] = $data['is_published'] ? now() : null;
        $data['created_by'] = Auth::id();

        $this->storeUploadedFiles($request, $data, 'content/notices');

        Notice::create($data);

        \App\Support\AuditTrail::log('notice_create', null, ['title' => $data['title']]);

        return back()->with('success', 'Notice created successfully.');
    }

    /**
     * Update a notice.
     */
    public function updateNotice(Request $request, Notice $notice)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'target_portal' => ['required', 'in:journalist,mediahouse,both'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published'] ? ($notice->published_at ?? now()) : null;

        $this->storeUploadedFiles($request, $data, 'content/notices');
        $this->cleanupOldFiles($notice, $data);

        $notice->update($data);

        \App\Support\AuditTrail::log('notice_update', null, ['notice_id' => $notice->id]);

        return back()->with('success', 'Notice updated successfully.');
    }

    /**
     * Delete a notice.
     */
    public function destroyNotice(Notice $notice)
    {
        $id = $notice->id;
        if ($notice->image_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($notice->image_path);
        if ($notice->attachment_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($notice->attachment_path);
        $notice->delete();

        \App\Support\AuditTrail::log('notice_delete', null, ['notice_id' => $id]);
        return back()->with('success', 'Notice deleted successfully.');
    }

    /**
     * Store a new event.
     */
    public function storeEvent(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:200'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'target_portal' => ['required', 'in:journalist,mediahouse,both'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? true);
        $data['published_at'] = $data['is_published'] ? now() : null;
        $data['created_by'] = Auth::id();
        $data['starts_at'] = $data['starts_at'] ?? now();

        $this->storeUploadedFiles($request, $data, 'content/events');

        Event::create($data);

        \App\Support\AuditTrail::log('event_create', null, ['title' => $data['title']]);

        return back()->with('success', 'Event created successfully.');
    }

    /**
     * Update an event.
     */
    public function updateEvent(Request $request, Event $event)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:200'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'target_portal' => ['required', 'in:journalist,mediahouse,both'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published'] ? ($event->published_at ?? now()) : null;
        $data['starts_at'] = $data['starts_at'] ?? $event->starts_at;

        $this->storeUploadedFiles($request, $data, 'content/events');
        $this->cleanupOldFiles($event, $data);

        $event->update($data);

        \App\Support\AuditTrail::log('event_update', null, ['event_id' => $event->id]);

        return back()->with('success', 'Event updated successfully.');
    }

    /**
     * Delete an event.
     */
    public function destroyEvent(Event $event)
    {
        $id = $event->id;
        if ($event->image_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($event->image_path);
        if ($event->attachment_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($event->attachment_path);
        $event->delete();

        \App\Support\AuditTrail::log('event_delete', null, ['event_id' => $id]);
        return back()->with('success', 'Event deleted successfully.');
    }

    /**
     * Helper: Store uploaded files.
     */
    private function storeUploadedFiles(Request $request, array &$data, string $folder): void
    {
        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $data['image_path'] = $img->store($folder . '/images', 'public');
        }

        if ($request->hasFile('attachment')) {
            $f = $request->file('attachment');
            $data['attachment_path'] = $f->store($folder . '/attachments', 'public');
            $data['attachment_original_name'] = $f->getClientOriginalName();
            $data['attachment_mime'] = $f->getMimeType();
        }
    }

    /**
     * Helper: Cleanup old files.
     */
    private function cleanupOldFiles($model, array $incoming): void
    {
        if (isset($incoming['image_path']) && $model->image_path && $model->image_path !== $incoming['image_path']) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($model->image_path);
        }
        if (isset($incoming['attachment_path']) && $model->attachment_path && $model->attachment_path !== $incoming['attachment_path']) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($model->attachment_path);
        }
    }

    /**
     * Store a new news item.
     */
    public function storeNews(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['slug'] = News::makeSlug($data['title']);
        $data['is_published'] = (bool)($data['is_published'] ?? true);
        $data['published_at'] = $data['is_published'] ? now() : null;
        $data['created_by'] = Auth::id();

        $this->storeUploadedFiles($request, $data, 'content/news');

        News::create($data);

        \App\Support\AuditTrail::log('news_create', null, ['title' => $data['title']]);

        return back()->with('success', 'News posted successfully.');
    }

    /**
     * Update a news item.
     */
    public function updateNews(Request $request, News $news)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        if ($news->title !== $data['title']) {
            $data['slug'] = News::makeSlug($data['title']);
        }

        $data['is_published'] = (bool)($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published'] ? ($news->published_at ?? now()) : null;

        $this->storeUploadedFiles($request, $data, 'content/news');
        $this->cleanupOldFiles($news, $data);

        $news->update($data);

        \App\Support\AuditTrail::log('news_update', null, ['news_id' => $news->id]);

        return back()->with('success', 'News updated successfully.');
    }

    /**
     * Delete a news item.
     */
    public function destroyNews(News $news)
    {
        $id = $news->id;
        if ($news->image_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($news->image_path);
        if ($news->attachment_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($news->attachment_path);
        $news->delete();

        \App\Support\AuditTrail::log('news_delete', null, ['news_id' => $id]);
        return back()->with('success', 'News deleted successfully.');
    }
}
