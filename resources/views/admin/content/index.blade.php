@extends('layouts.portal')
@section('title','Notices & Events')
@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Notices & Events</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        Manage public announcements for Media Practitioner Accreditation Portal and Media House Registration Portal.
      </div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  <div class="row g-3">
    {{-- Notices --}}
    <div class="col-12 col-lg-6">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold m-0"><i class="ri-megaphone-line me-2" style="color:var(--zmc-accent)"></i>Notices</h6>
          @hasanyrole('super_admin|it_admin|pr')
            <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#createNotice"><i class="ri-add-line me-1"></i>New</button>
          @endhasanyrole
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
                  @hasanyrole('super_admin|it_admin|pr')
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editNotice{{ $n->id }}"><i class="ri-edit-line"></i></button>
                    <form method="POST" action="{{ route('admin.content.notices.destroy',$n) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this notice?')"><i class="ri-delete-bin-line"></i></button>
                    </form>
                  @else
                    <span class="text-muted small">View Only</span>
                  @endhasanyrole
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
    <div class="col-12 col-lg-6">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold m-0"><i class="ri-calendar-event-line me-2" style="color:var(--zmc-accent)"></i>Events</h6>
          @hasanyrole('super_admin|it_admin|pr')
            <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#createEvent"><i class="ri-add-line me-1"></i>New</button>
          @endhasanyrole
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
                  @hasanyrole('super_admin|it_admin|pr')
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editEvent{{ $e->id }}"><i class="ri-edit-line"></i></button>
                    <form method="POST" action="{{ route('admin.content.events.destroy',$e) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this event?')"><i class="ri-delete-bin-line"></i></button>
                    </form>
                  @else
                    <span class="text-muted small">View Only</span>
                  @endhasanyrole
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3">{{ $events->links() }}</div>
      </div>
    </div>

    {{-- Vacancies --}}
    <div class="col-12 col-lg-6">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold m-0"><i class="ri-id-card-line me-2" style="color:var(--zmc-accent)"></i>Vacancies</h6>
          @hasanyrole('super_admin|it_admin|pr')
            <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#createVacancy"><i class="ri-add-line me-1"></i>New</button>
          @endhasanyrole
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead><tr><th>Title</th><th>Closing</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @foreach($vacancies as $v)
              <tr>
                <td class="fw-bold">{{ $v->title }}</td>
                <td class="small">{{ optional($v->closing_at)->format('d M Y') ?? '—' }}</td>
                <td>
                  <span class="badge rounded-pill bg-{{ $v->is_published ? 'success' : 'secondary' }} px-3">{{ $v->is_published ? 'Published' : 'Draft' }}</span>
                </td>
                <td class="text-end">
                  @hasanyrole('super_admin|it_admin|pr')
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editVacancy{{ $v->id }}"><i class="ri-edit-line"></i></button>
                    <form method="POST" action="{{ route('admin.content.vacancies.destroy',$v) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this vacancy?')"><i class="ri-delete-bin-line"></i></button>
                    </form>
                  @else
                    <span class="text-muted small">View Only</span>
                  @endhasanyrole
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3">{{ $vacancies->links() }}</div>
      </div>
    </div>

    {{-- Tenders --}}
    <div class="col-12 col-lg-6">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold m-0"><i class="ri-briefcase-line me-2" style="color:var(--zmc-accent)"></i>Tenders</h6>
          @hasanyrole('super_admin|it_admin|pr')
            <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#createTender"><i class="ri-add-line me-1"></i>New</button>
          @endhasanyrole
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead><tr><th>Title</th><th>Closing</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @foreach($tenders as $t)
              <tr>
                <td class="fw-bold">{{ $t->title }}</td>
                <td class="small">{{ optional($t->closing_at)->format('d M Y') ?? '—' }}</td>
                <td>
                  <span class="badge rounded-pill bg-{{ $t->is_published ? 'success' : 'secondary' }} px-3">{{ $t->is_published ? 'Published' : 'Draft' }}</span>
                </td>
                <td class="text-end">
                  @hasanyrole('super_admin|it_admin|pr')
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTender{{ $t->id }}"><i class="ri-edit-line"></i></button>
                    <form method="POST" action="{{ route('admin.content.tenders.destroy',$t) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this tender?')"><i class="ri-delete-bin-line"></i></button>
                    </form>
                  @else
                    <span class="text-muted small">View Only</span>
                  @endhasanyrole
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3">{{ $tenders->links() }}</div>
      </div>
    </div>
  </div>
</div>

@hasanyrole('super_admin|it_admin|pr')
{{-- Create Notice --}}
<div class="modal fade" id="createNotice" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ Route::has('admin.content.notices.store') ? route('admin.content.notices.store') : route('content.notices.store') }}">
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

{{-- Create Event --}}
<div class="modal fade" id="createEvent" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ Route::has('admin.content.events.store') ? route('admin.content.events.store') : route('content.events.store') }}">
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

{{-- Edit modals --}}
@foreach($notices as $n)
<div class="modal fade" id="editNotice{{ $n->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.content.notices.update',$n) }}">
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
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.content.events.update',$e) }}">
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

{{-- Create Vacancy Modal --}}
<div class="modal fade" id="createVacancy" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.content.vacancies.store') }}">
      @csrf
      <div class="modal-header zmc-modal-header"><div class="zmc-modal-title">New Vacancy</div><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label zmc-lbl">Title</label><input class="form-control zmc-input" name="title" required></div>
          <div class="col-12"><label class="form-label zmc-lbl">Description / Body</label><textarea class="form-control zmc-input" rows="5" name="body" required></textarea></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Closing Date</label><input class="form-control zmc-input" type="date" name="closing_at"></div>
          <div class="col-12 col-md-3"><label class="form-label zmc-lbl">Image</label><input class="form-control zmc-input" type="file" name="image" accept="image/*"></div>
          <div class="col-12 col-md-3"><label class="form-label zmc-lbl">Attachment</label><input class="form-control zmc-input" type="file" name="attachment"></div>
          <div class="col-12 d-flex align-items-end">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_published" value="1" checked id="v_pub"><label class="form-check-label" for="v_pub">Publish Immediately</label></div>
          </div>
        </div>
      </div>
      <div class="modal-footer zmc-modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-dark" type="submit">Save Vacancy</button></div>
    </form>
  </div>
</div>

@foreach($vacancies as $v)
<div class="modal fade" id="editVacancy{{ $v->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.content.vacancies.update',$v) }}">
      @csrf @method('PUT')
      <div class="modal-header zmc-modal-header"><div class="zmc-modal-title">Edit Vacancy</div><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label zmc-lbl">Title</label><input class="form-control zmc-input" name="title" value="{{ $v->title }}" required></div>
          <div class="col-12"><label class="form-label zmc-lbl">Description</label><textarea class="form-control zmc-input" rows="5" name="body" required>{{ $v->body }}</textarea></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Closing Date</label><input class="form-control zmc-input" type="date" name="closing_at" value="{{ optional($v->closing_at)->format('Y-m-d') }}"></div>
          <div class="col-12 col-md-3"><label class="form-label zmc-lbl">Replace Image</label><input class="form-control zmc-input" type="file" name="image" accept="image/*"></div>
          <div class="col-12 col-md-3"><label class="form-label zmc-lbl">Replace Attachment</label><input class="form-control zmc-input" type="file" name="attachment"></div>
          <div class="col-12 d-flex align-items-end">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_published" value="1" @checked($v->is_published) id="v_pub{{ $v->id }}"><label class="form-check-label" for="v_pub{{ $v->id }}">Published</label></div>
          </div>
        </div>
      </div>
      <div class="modal-footer zmc-modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-dark" type="submit">Update Vacancy</button></div>
    </form>
  </div>
</div>
@endforeach

{{-- Create Tender Modal --}}
<div class="modal fade" id="createTender" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.content.tenders.store') }}">
      @csrf
      <div class="modal-header zmc-modal-header"><div class="zmc-modal-title">New Tender</div><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label zmc-lbl">Title / Tender Ref</label><input class="form-control zmc-input" name="title" required></div>
          <div class="col-12"><label class="form-label zmc-lbl">Description</label><textarea class="form-control zmc-input" rows="5" name="description" required></textarea></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Closing Date</label><input class="form-control zmc-input" type="date" name="closing_at"></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Document Attachment</label><input class="form-control zmc-input" type="file" name="attachment"></div>
          <div class="col-12 d-flex align-items-end">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_published" value="1" checked id="t_pub"><label class="form-check-label" for="t_pub">Publish Immediately</label></div>
          </div>
        </div>
      </div>
      <div class="modal-footer zmc-modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-dark" type="submit">Save Tender</button></div>
    </form>
  </div>
</div>

@foreach($tenders as $t)
<div class="modal fade" id="editTender{{ $t->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.content.tenders.update',$t) }}">
      @csrf @method('PUT')
      <div class="modal-header zmc-modal-header"><div class="zmc-modal-title">Edit Tender</div><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label zmc-lbl">Title / Tender Ref</label><input class="form-control zmc-input" name="title" value="{{ $t->title }}" required></div>
          <div class="col-12"><label class="form-label zmc-lbl">Description</label><textarea class="form-control zmc-input" rows="5" name="description" required>{{ $t->description }}</textarea></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Closing Date</label><input class="form-control zmc-input" type="date" name="closing_at" value="{{ optional($t->closing_at)->format('Y-m-d') }}"></div>
          <div class="col-12 col-md-6"><label class="form-label zmc-lbl">Replace Attachment</label><input class="form-control zmc-input" type="file" name="attachment"></div>
          <div class="col-12 d-flex align-items-end">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_published" value="1" @checked($t->is_published) id="t_pub{{ $t->id }}"><label class="form-check-label" for="t_pub{{ $t->id }}">Published</label></div>
          </div>
        </div>
      </div>
      <div class="modal-footer zmc-modal-footer"><button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-dark" type="submit">Update Tender</button></div>
    </form>
  </div>
</div>
@endforeach
@endhasanyrole
@endsection
