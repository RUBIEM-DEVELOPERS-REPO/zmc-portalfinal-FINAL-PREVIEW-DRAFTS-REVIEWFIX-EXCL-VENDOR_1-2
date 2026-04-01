<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Event;
use App\Models\Vacancy;
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function __construct()
    {
        // View access: Super Admin, IT Admin, Director, PR
        // Create/update/delete is restricted in the action methods.
        $this->middleware(['auth', 'role:super_admin|it_admin|director|pr']);
    }

    public function index()
    {
        // Role-gated instead of permission-gated to avoid 403s when permissions are not yet seeded/cached.
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','director','pr']), 403);

        $notices   = Notice::orderByDesc('id')->paginate(15, ['*'], 'notices');
        $events    = Event::orderByDesc('starts_at')->paginate(15, ['*'], 'events');
        $vacancies = Vacancy::orderByDesc('id')->paginate(15, ['*'], 'vacancies');
        $tenders   = Tender::orderByDesc('id')->paginate(15, ['*'], 'tenders');

        return view('admin.content.index', compact('notices','events','vacancies','tenders'));
    }

    private function storeUploadedFiles(Request $request, array &$data, string $folder): void
    {
        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $data['image_path'] = $img->store($folder.'/images', 'public');
        }

        if ($request->hasFile('attachment')) {
            $f = $request->file('attachment');
            $data['attachment_path'] = $f->store($folder.'/attachments', 'public');
            $data['attachment_original_name'] = $f->getClientOriginalName();
            $data['attachment_mime'] = $f->getMimeType();
        }
    }

    private function cleanupOldFiles($model, array $incoming): void
    {
        // If we are replacing, delete old ones
        if (isset($incoming['image_path']) && $model->image_path && $model->image_path !== $incoming['image_path']) {
            Storage::disk('public')->delete($model->image_path);
        }
        if (isset($incoming['attachment_path']) && $model->attachment_path && $model->attachment_path !== $incoming['attachment_path']) {
            Storage::disk('public')->delete($model->attachment_path);
        }
    }

    public function storeNotice(Request $request)
    {
        // Uploading allowed for Super Admin + IT Admin + PR only
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','pr']), 403);

        $data = $request->validate([
            'title' => ['required','string','max:200'],
            'body' => ['required','string'],
            'target_portal' => ['required','in:journalist,mediahouse,both'],
            'is_published' => ['nullable','boolean'],
            'image' => ['nullable','image','max:5120'],
            'attachment' => ['nullable','file','max:10240','mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? true);
        $data['published_at'] = $data['is_published'] ? now() : null;
        $data['created_by'] = Auth::id();

        $this->storeUploadedFiles($request, $data, 'content/notices');

        Notice::create($data);

        \App\Support\AuditTrail::log('notice_create', null, ['title'=>$data['title']]);

        return back()->with('success','Notice created.');
    }

    public function updateNotice(Request $request, Notice $notice)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','pr']), 403);

        $data = $request->validate([
            'title' => ['required','string','max:200'],
            'body' => ['required','string'],
            'target_portal' => ['required','in:journalist,mediahouse,both'],
            'is_published' => ['nullable','boolean'],
            'image' => ['nullable','image','max:5120'],
            'attachment' => ['nullable','file','max:10240','mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published'] ? ($notice->published_at ?? now()) : null;

        $this->storeUploadedFiles($request, $data, 'content/notices');
        $this->cleanupOldFiles($notice, $data);

        $notice->update($data);

        \App\Support\AuditTrail::log('notice_update', null, ['notice_id'=>$notice->id]);

        return back()->with('success','Notice updated.');
    }

    public function destroyNotice(Notice $notice)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','pr']), 403);

        $id = $notice->id;
        if ($notice->image_path) Storage::disk('public')->delete($notice->image_path);
        if ($notice->attachment_path) Storage::disk('public')->delete($notice->attachment_path);
        $notice->delete();

        \App\Support\AuditTrail::log('notice_delete', null, ['notice_id'=>$id]);
        return back()->with('success','Notice deleted.');
    }

    public function storeEvent(Request $request)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','pr']), 403);

        $data = $request->validate([
            'title' => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'location' => ['nullable','string','max:200'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
            'target_portal' => ['required','in:journalist,mediahouse,both'],
            'is_published' => ['nullable','boolean'],
            'image' => ['nullable','image','max:5120'],
            'attachment' => ['nullable','file','max:10240','mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? true);
        $data['published_at'] = $data['is_published'] ? now() : null;
        $data['created_by'] = Auth::id();

        // starts_at is required by DB, default to now if empty
        $data['starts_at'] = $data['starts_at'] ?? now();

        $this->storeUploadedFiles($request, $data, 'content/events');

        Event::create($data);

        \App\Support\AuditTrail::log('event_create', null, ['title'=>$data['title']]);

        return back()->with('success','Event created.');
    }

    public function updateEvent(Request $request, Event $event)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','pr']), 403);

        $data = $request->validate([
            'title' => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'location' => ['nullable','string','max:200'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
            'target_portal' => ['required','in:journalist,mediahouse,both'],
            'is_published' => ['nullable','boolean'],
            'image' => ['nullable','image','max:5120'],
            'attachment' => ['nullable','file','max:10240','mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published'] ? ($event->published_at ?? now()) : null;
        $data['starts_at'] = $data['starts_at'] ?? $event->starts_at;

        $this->storeUploadedFiles($request, $data, 'content/events');
        $this->cleanupOldFiles($event, $data);

        $event->update($data);

        \App\Support\AuditTrail::log('event_update', null, ['event_id'=>$event->id]);

        return back()->with('success','Event updated.');
    }

    public function destroyEvent(Event $event)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','pr']), 403);

        $id = $event->id;
        if ($event->image_path) Storage::disk('public')->delete($event->image_path);
        if ($event->attachment_path) Storage::disk('public')->delete($event->attachment_path);
        $event->delete();

        \App\Support\AuditTrail::log('event_delete', null, ['event_id'=>$id]);
        return back()->with('success','Event deleted.');
    }

    /* Vacancy Methods */
    public function storeVacancy(Request $request)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'it_admin', 'pr']), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'closing_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? true);
        $data['published_at'] = $data['is_published'] ? now() : null;
        $data['created_by']   = Auth::id();

        $this->storeUploadedFiles($request, $data, 'content/vacancies');
        Vacancy::create($data);

        \App\Support\AuditTrail::log('vacancy_create', null, ['title' => $data['title']]);
        return back()->with('success', 'Vacancy created.');
    }

    public function updateVacancy(Request $request, Vacancy $vacancy)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'it_admin', 'pr']), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'closing_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published'] ? ($vacancy->published_at ?? now()) : null;

        $this->storeUploadedFiles($request, $data, 'content/vacancies');
        $this->cleanupOldFiles($vacancy, $data);

        $vacancy->update($data);

        \App\Support\AuditTrail::log('vacancy_update', null, ['vacancy_id' => $vacancy->id]);
        return back()->with('success', 'Vacancy updated.');
    }

    public function destroyVacancy(Vacancy $vacancy)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'it_admin', 'pr']), 403);
        $id = $vacancy->id;
        if ($vacancy->image_path) Storage::disk('public')->delete($vacancy->image_path);
        if ($vacancy->attachment_path) Storage::disk('public')->delete($vacancy->attachment_path);
        $vacancy->delete();

        \App\Support\AuditTrail::log('vacancy_delete', null, ['vacancy_id' => $id]);
        return back()->with('success', 'Vacancy deleted.');
    }

    /* Tender Methods */
    public function storeTender(Request $request)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'it_admin', 'pr']), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'closing_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? true);
        $data['published_at'] = $data['is_published'] ? now() : null;
        $data['created_by']   = Auth::id();

        $this->storeUploadedFiles($request, $data, 'content/tenders');
        Tender::create($data);

        \App\Support\AuditTrail::log('tender_create', null, ['title' => $data['title']]);
        return back()->with('success', 'Tender created.');
    }

    public function updateTender(Request $request, Tender $tender)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'it_admin', 'pr']), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'closing_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['is_published'] = (bool)($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published'] ? ($tender->published_at ?? now()) : null;

        $this->storeUploadedFiles($request, $data, 'content/tenders');
        $this->cleanupOldFiles($tender, $data);

        $tender->update($data);

        \App\Support\AuditTrail::log('tender_update', null, ['tender_id' => $tender->id]);
        return back()->with('success', 'Tender updated.');
    }

    public function destroyTender(Tender $tender)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'it_admin', 'pr']), 403);
        $id = $tender->id;
        if ($tender->attachment_path) Storage::disk('public')->delete($tender->attachment_path);
        $tender->delete();

        \App\Support\AuditTrail::log('tender_delete', null, ['tender_id' => $id]);
        return back()->with('success', 'Tender deleted.');
    }
}
