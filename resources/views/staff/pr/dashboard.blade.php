@extends('layouts.portal')
@section('title','Public Relations Dashboard - Notices, Events & News')
@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">
        <i class="ri-megaphone-line me-2" style="color:var(--zmc-accent);"></i>Notices, Events & News
      </h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        Manage public announcements, events, and news for Media Practitioner Accreditation Portal and Media House Registration Portal.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ri-arrow-left-line me-1"></i>Back to Dashboard
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  <div class="row g-3 mb-4">
    {{-- Notices --}}
    <div class="col-12 col-lg-4">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold m-0"><i class="ri-megaphone-line me-2" style="color:var(--zmc-accent)"></i>Notices</h6>
          <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#createNotice"><i class="ri-add-line me-1"></i>New</button>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead><tr><th>Title</th><th>Portal</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @foreach($notices as $n)
              <tr>
                <td class="fw-bold">
                  @if($n->image_path)
                    <img src="{{ asset('storage/' . $n->image_path) }}" alt="" class="rounded me-2" style="width:32px;height:32px;object-fit:cover;vertical-align:middle;">
                  @endif
                  {{ $n->title }}
                </td>
                <td class="small text-muted text-uppercase">{{ $n->target_portal }}</td>
                <td>
                  <span class="badge rounded-pill bg-{{ $n->is_published ? 'success' : 'secondary' }} px-3">{{ $n->is_published ? 'Published' : 'Draft' }}</span>
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editNotice{{ $n->id }}"><i class="ri-edit-line"></i></button>
                  <form method="POST" action="{{ route('staff.pr.notices.destroy',$n) }}" class="d-inline">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this notice?')"><i class="ri-delete-bin-line"></i></button>
                  </form>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3">{{ $notices->links() }}</div>
      </div>
    </div>

    {{-- Events --}}
    <div class="col-12 col-lg-4">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold m-0"><i class="ri-calendar-event-line me-2" style="color:var(--zmc-accent)"></i>Events</h6>
          <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#createEvent"><i class="ri-add-line me-1"></i>New</button>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead><tr><th>Title</th><th>Date</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @foreach($events as $e)
              <tr>
                <td class="fw-bold">
                  @if($e->image_path)
                    <img src="{{ asset('storage/' . $e->image_path) }}" alt="" class="rounded me-2" style="width:32px;height:32px;object-fit:cover;vertical-align:middle;">
                  @endif
                  {{ $e->title }}
                </td>
                <td class="small">{{ optional($e->starts_at)->format('d M Y') ?? '—' }}</td>
                <td>
                  <span class="badge rounded-pill bg-{{ $e->is_published ? 'success' : 'secondary' }} px-3">{{ $e->is_published ? 'Published' : 'Draft' }}</span>
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editEvent{{ $e->id }}"><i class="ri-edit-line"></i></button>
                  <form method="POST" action="{{ route('staff.pr.events.destroy',$e) }}" class="d-inline">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this event?')"><i class="ri-delete-bin-line"></i></button>
                  </form>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3">{{ $events->links() }}</div>
      </div>
    </div>

    {{-- News --}}
    <div class="col-12 col-lg-4">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold m-0"><i class="ri-newspaper-line me-2" style="color:var(--zmc-accent)"></i>News</h6>
          <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#createNews"><i class="ri-add-line me-1"></i>New</button>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead><tr><th>Title</th><th>Published</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @foreach($news as $n)
              <tr>
                <td class="fw-bold">
                  {{ $n->title }}
                  <div class="small text-muted">Slug: <code>{{ $n->slug }}</code></div>
                </td>
                <td class="small">{{ $n->published_at ? $n->published_at->format('d M Y H:i') : '—' }}</td>
                <td>
                  <span class="badge rounded-pill bg-{{ $n->is_published ? 'success' : 'secondary' }} px-3">{{ $n->is_published ? 'Published' : 'Draft' }}</span>
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editNews{{ $n->id }}"><i class="ri-edit-line"></i></button>
                  <form method="POST" action="{{ route('staff.pr.news.destroy',$n) }}" class="d-inline">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this news item?')"><i class="ri-delete-bin-line"></i></button>
                  </form>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3">{{ $news->links() }}</div>
      </div>
    </div>
  </div>
</div>

{{-- Create Notice --}}
<div class="modal fade" id="createNotice" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('staff.pr.notices.store') }}">
      @csrf
      <div class="modal-header zmc-modal-header"><div class="zmc-modal-title">Create Notice</div><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label zmc-lbl">Title</label><input class="form-control zmc-input" name="title" required></div>
          <div class="col-12"><label class="form-label zmc-lbl">Body</label><textarea class="form-control zmc-input" rows="5" name="body" required></textarea></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Image (optional)</label><input class="form-control zmc-input" type="file" name="image" accept="image/*"></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Attachment (optional)</label><input class="form-control zmc-input" type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,image/*"></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Portal</label>
            <select class="form-select zmc-input" name="target_portal" required>
              <option value="both">Both</option><option value="journalist">Media Practitioner</option><option value="mediahouse">Media House</option>
            </select>
          </div>
          <div class="col-12 col-md-6 d-flex align-items-end">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_published" value="1" checked id="n_pub"><label class="form-check-label" for="n_pub">Publish</label></div>
          </div>
        </div>
      </div>
      <div class="modal-footer zmc-modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-dark" type="submit">Save</button></div>
    </form>
  </div>
</div>

{{-- Create Event --}}
<div class="modal fade" id="createEvent" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('staff.pr.events.store') }}">
      @csrf
      <div class="modal-header zmc-modal-header"><div class="zmc-modal-title">Create Event</div><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label zmc-lbl">Title</label><input class="form-control zmc-input" name="title" required></div>
          <div class="col-12"><label class="form-label zmc-lbl">Description</label><textarea class="form-control zmc-input" rows="4" name="description"></textarea></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Image (optional)</label><input class="form-control zmc-input" type="file" name="image" accept="image/*"></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Attachment (optional)</label><input class="form-control zmc-input" type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,image/*"></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Location</label><input class="form-control zmc-input" name="location"></div>
          <div class="col-12 col-md-3"><label class="form-label zmc-lbl">Starts</label><input class="form-control zmc-input" type="datetime-local" name="starts_at"></div>
          <div class="col-12 col-md-3"><label class="form-label zmc-lbl">Ends</label><input class="form-control zmc-input" type="datetime-local" name="ends_at"></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Portal</label>
            <select class="form-select zmc-input" name="target_portal" required>
              <option value="both">Both</option><option value="journalist">Media Practitioner</option><option value="mediahouse">Media House</option>
            </select>
          </div>
          <div class="col-12 col-md-6 d-flex align-items-end">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_published" value="1" checked id="e_pub"><label class="form-check-label" for="e_pub">Publish</label></div>
          </div>
        </div>
      </div>
      <div class="modal-footer zmc-modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-dark" type="submit">Save</button></div>
    </form>
  </div>
</div>

{{-- Edit modals --}}
@foreach($notices as $n)
<div class="modal fade" id="editNotice{{ $n->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('staff.pr.notices.update',$n) }}">
      @csrf @method('PUT')
      <div class="modal-header zmc-modal-header"><div class="zmc-modal-title">Edit Notice</div><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label zmc-lbl">Title</label><input class="form-control zmc-input" name="title" value="{{ $n->title }}" required></div>
          <div class="col-12"><label class="form-label zmc-lbl">Body</label><textarea class="form-control zmc-input" rows="5" name="body" required>{{ $n->body }}</textarea></div>
          <div class="col-12 col-md-6">
            <label class="form-label zmc-lbl">Replace Image (optional)</label>
            <input class="form-control zmc-input" type="file" name="image" accept="image/*">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label zmc-lbl">Replace Attachment (optional)</label>
            <input class="form-control zmc-input" type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,image/*">
          </div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Portal</label>
            <select class="form-select zmc-input" name="target_portal" required>
              <option value="both" @selected($n->target_portal==='both')>Both</option>
              <option value="journalist" @selected($n->target_portal==='journalist')>Media Practitioner</option>
              <option value="mediahouse" @selected($n->target_portal==='mediahouse')>Media House</option>
            </select>
          </div>
          <div class="col-12 col-md-6 d-flex align-items-end">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_published" value="1" @checked($n->is_published) id="n_pub{{ $n->id }}"><label class="form-check-label" for="n_pub{{ $n->id }}">Publish</label></div>
          </div>
        </div>
      </div>
      <div class="modal-footer zmc-modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-dark" type="submit">Save</button></div>
    </form>
  </div>
</div>
@endforeach

@foreach($events as $e)
<div class="modal fade" id="editEvent{{ $e->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('staff.pr.events.update',$e) }}">
      @csrf @method('PUT')
      <div class="modal-header zmc-modal-header"><div class="zmc-modal-title">Edit Event</div><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label zmc-lbl">Title</label><input class="form-control zmc-input" name="title" value="{{ $e->title }}" required></div>
          <div class="col-12"><label class="form-label zmc-lbl">Description</label><textarea class="form-control zmc-input" rows="4" name="description">{{ $e->description }}</textarea></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Location</label><input class="form-control zmc-input" name="location" value="{{ $e->location }}"></div>
          <div class="col-12 col-md-3"><label class="form-label zmc-lbl">Starts</label><input class="form-control zmc-input" type="datetime-local" name="starts_at" value="{{ optional($e->starts_at)->format('Y-m-d\TH:i') }}"></div>
          <div class="col-12 col-md-3"><label class="form-label zmc-lbl">Ends</label><input class="form-control zmc-input" type="datetime-local" name="ends_at" value="{{ optional($e->ends_at)->format('Y-m-d\TH:i') }}"></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Replace Image (optional)</label><input class="form-control zmc-input" type="file" name="image" accept="image/*"></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Replace Attachment (optional)</label><input class="form-control zmc-input" type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,image/*"></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Portal</label>
            <select class="form-select zmc-input" name="target_portal" required>
              <option value="both" @selected($e->target_portal==='both')>Both</option>
              <option value="journalist" @selected($e->target_portal==='journalist')>Media Practitioner</option>
              <option value="mediahouse" @selected($e->target_portal==='mediahouse')>Media House</option>
            </select>
          </div>
          <div class="col-12 col-md-6 d-flex align-items-end">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_published" value="1" @checked($e->is_published) id="e_pub{{ $e->id }}"><label class="form-check-label" for="e_pub{{ $e->id }}">Publish</label></div>
          </div>
        </div>
      </div>
      <div class="modal-footer zmc-modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-dark" type="submit">Save</button></div>
    </form>
  </div>
</div>
@endforeach

{{-- Create News --}}
<div class="modal fade" id="createNews" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('staff.pr.news.store') }}">
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

{{-- Edit News --}}
@foreach($news as $n)
<div class="modal fade" id="editNews{{ $n->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('staff.pr.news.update',$n) }}">
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
@endsection
