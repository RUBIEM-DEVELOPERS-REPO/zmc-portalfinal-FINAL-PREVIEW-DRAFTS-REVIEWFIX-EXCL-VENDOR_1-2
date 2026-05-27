<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function __construct()
    {
        // View access: Super Admin, IT Admin, Director, PR Officer
        // Create/update/delete is restricted in the action methods.
        $this->middleware(['auth', 'role:super_admin|it_admin|director|pr_officer']);
    }

    public function index()
    {
        // Role-gated instead of permission-gated to avoid 403s when permissions are not yet seeded/cached.
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','director','pr_officer']), 403);

        $items = News::orderByDesc('published_at')->orderByDesc('id')->paginate(15);
        return view('admin.news.index', compact('items'));
    }

    private function storeFiles(Request $request, array &$data): void
    {
        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $data['image_path'] = $img->store('content/news/images', 'public');
        }
        if ($request->hasFile('attachment')) {
            $f = $request->file('attachment');
            $data['attachment_path'] = $f->store('content/news/attachments', 'public');
            $data['attachment_original_name'] = $f->getClientOriginalName();
            $data['attachment_mime'] = $f->getMimeType();
        }
    }

    private function cleanupOldFiles(News $news, array $incoming): void
    {
        if (isset($incoming['image_path']) && $news->image_path && $news->image_path !== $incoming['image_path']) {
            Storage::disk('public')->delete($news->image_path);
        }
        if (isset($incoming['attachment_path']) && $news->attachment_path && $news->attachment_path !== $incoming['attachment_path']) {
            Storage::disk('public')->delete($news->attachment_path);
        }
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','pr_officer']), 403);

        $data = $request->validate([
            'title' => ['required','string','max:200'],
            'body' => ['required','string'],
            'is_published' => ['nullable','boolean'],
            'image' => ['nullable','image','max:5120'],
            'attachment' => ['nullable','file','max:10240','mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        $data['slug'] = News::makeSlug($data['title']);
        $data['is_published'] = (bool)($data['is_published'] ?? true);
        $data['published_at'] = $data['is_published'] ? now() : null;
        $data['created_by'] = Auth::id();

        $this->storeFiles($request, $data);

        News::create($data);

        \App\Support\AuditTrail::log('news_create', null, ['title'=>$data['title']]);

        return back()->with('success','News posted.');
    }

    public function update(Request $request, News $news)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','pr_officer']), 403);

        $data = $request->validate([
            'title' => ['required','string','max:200'],
            'body' => ['required','string'],
            'is_published' => ['nullable','boolean'],
            'image' => ['nullable','image','max:5120'],
            'attachment' => ['nullable','file','max:10240','mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp'],
        ]);

        if ($news->title !== $data['title']) {
            $data['slug'] = News::makeSlug($data['title']);
        }

        $data['is_published'] = (bool)($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published'] ? ($news->published_at ?? now()) : null;

        $this->storeFiles($request, $data);
        $this->cleanupOldFiles($news, $data);

        $news->update($data);

        \App\Support\AuditTrail::log('news_update', null, ['news_id'=>$news->id]);

        return back()->with('success','News updated.');
    }

    public function destroy(News $news)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','pr_officer']), 403);

        $id = $news->id;
        if ($news->image_path) Storage::disk('public')->delete($news->image_path);
        if ($news->attachment_path) Storage::disk('public')->delete($news->attachment_path);
        $news->delete();

        \App\Support\AuditTrail::log('news_delete', null, ['news_id'=>$id]);

        return back()->with('success','News deleted.');
    }
}
