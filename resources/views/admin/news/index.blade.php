@extends('layouts.portal')
@section('title','News')
@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">News</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        Manage news posts that can be consumed by the website via JSON endpoints.
      </div>
    </div>
    @hasanyrole('super_admin|it_admin|pr_officer')
      <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#createNews"><i class="ri-add-line me-1"></i>New</button>
    @endhasanyrole
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  <div class="zmc-card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Status</th>
            <th>Published</th>
            <th>Media</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
        @foreach($items as $n)
          <tr>
            <td class="fw-bold">
              {{ $n->title }}
              <div class="small text-muted">Slug: <code>{{ $n->slug }}</code></div>
            </td>
            <td>
              <span class="badge rounded-pill bg-{{ $n->is_published ? 'success' : 'secondary' }} px-3">{{ $n->is_published ? 'Published' : 'Draft' }}</span>
            </td>
            <td class="small">{{ $n->published_at ? $n->published_at->format('d M Y H:i') : '—' }}</td>
            <td class="small">
              @if($n->image_path)
                <a href="{{ asset('storage/'.$n->image_path) }}" target="_blank" class="me-2">Image</a>
              @endif
              @if($n->attachment_path)
                <a href="{{ asset('storage/'.$n->attachment_path) }}" target="_blank">{{ $n->attachment_original_name ?? 'Attachment' }}</a>
              @endif
              @if(!$n->image_path && !$n->attachment_path)
                —
              @endif
            </td>
            <td class="text-end">
              @hasanyrole('super_admin|it_admin|pr_officer')
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editNews{{ $n->id }}"><i class="ri-edit-line"></i></button>
                <form method="POST" action="{{ route('admin.news.destroy',$n) }}" class="d-inline">@csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this news item?')"><i class="ri-delete-bin-line"></i></button>
                </form>
              @else
                —
              @endhasanyrole
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $items->links() }}</div>
  </div>
</div>

{{-- Create News --}}
@hasanyrole('super_admin|it_admin|pr_officer')
<div class="modal fade" id="createNews" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.news.store') }}">
      @csrf
      <div class="modal-header zmc-modal-header"><div class="zmc-modal-title">Create News</div><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label zmc-lbl">Title</label><input class="form-control zmc-input" name="title" required></div>
          <div class="col-12"><label class="form-label zmc-lbl">Body</label><textarea class="form-control zmc-input" rows="6" name="body" required></textarea></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Image (optional)</label><input class="form-control zmc-input" type="file" name="image" accept="image/*"></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Attachment (optional)</label><input class="form-control zmc-input" type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,image/*"></div>
          <div class="col-12">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_published" value="1" checked id="news_pub"><label class="form-check-label" for="news_pub">Publish</label></div>
          </div>
        </div>
      </div>
      <div class="modal-footer zmc-modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-dark" type="submit">Save</button></div>
    </form>
  </div>
</div>
@endhasanyrole

{{-- Edit News --}}
@hasanyrole('super_admin|it_admin|pr_officer')
@foreach($items as $n)
<div class="modal fade" id="editNews{{ $n->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.news.update',$n) }}">
      @csrf @method('PUT')
      <div class="modal-header zmc-modal-header"><div class="zmc-modal-title">Edit News</div><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label zmc-lbl">Title</label><input class="form-control zmc-input" name="title" value="{{ $n->title }}" required></div>
          <div class="col-12"><label class="form-label zmc-lbl">Body</label><textarea class="form-control zmc-input" rows="6" name="body" required>{{ $n->body }}</textarea></div>
          <div class="col-12 col-md-6">
            <label class="form-label zmc-lbl">Replace Image (optional)</label>
            <input class="form-control zmc-input" type="file" name="image" accept="image/*">
            @if($n->image_path)
              <div class="small text-muted mt-1">Current: <a href="{{ asset('storage/'.$n->image_path) }}" target="_blank">View image</a></div>
            @endif
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label zmc-lbl">Replace Attachment (optional)</label>
            <input class="form-control zmc-input" type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,image/*">
            @if($n->attachment_path)
              <div class="small text-muted mt-1">Current: <a href="{{ asset('storage/'.$n->attachment_path) }}" target="_blank">{{ $n->attachment_original_name ?? 'Download' }}</a></div>
            @endif
          </div>
          <div class="col-12">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_published" value="1" @checked($n->is_published) id="news_pub{{ $n->id }}"><label class="form-check-label" for="news_pub{{ $n->id }}">Publish</label></div>
          </div>
        </div>
      </div>
      <div class="modal-footer zmc-modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-dark" type="submit">Save</button></div>
    </form>
  </div>
</div>
@endforeach
@endhasanyrole
@endsection
