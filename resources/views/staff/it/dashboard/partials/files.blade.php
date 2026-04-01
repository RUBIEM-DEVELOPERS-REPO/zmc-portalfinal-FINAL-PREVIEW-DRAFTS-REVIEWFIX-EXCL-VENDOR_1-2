<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="fw-bold m-0">Storage & Attachment Explorer</h6>
            <p class="text-slate-600 small m-0 fw-medium">Grouped by application/applicant</p>
        </div>
        
        <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-2">
            <input type="hidden" name="tab" value="files">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white text-slate-400 border-end-0"><i class="ri-calendar-line"></i></span>
                <input type="date" name="f_date_from" value="{{ request('f_date_from') }}" class="form-control border-start-0 ps-0" placeholder="From">
            </div>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white text-slate-400 border-end-0"><i class="ri-calendar-line"></i></span>
                <input type="date" name="f_date_to" value="{{ request('f_date_to') }}" class="form-control border-start-0 ps-0" placeholder="To">
            </div>
            <button type="submit" class="btn btn-slate-900 btn-sm px-3 fw-bold rounded-pill">Filter</button>
            @if(request('f_date_from') || request('f_date_to'))
                <a href="{{ url()->current() }}?tab=files" class="btn btn-slate-100 btn-sm rounded-pill"><i class="ri-refresh-line"></i></a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle m-0">
                <thead class="bg-slate-50 border-top border-bottom">
                    <tr>
                        <th class="ps-4 small text-slate-700 uppercase fw-bold py-3">Document Name</th>
                        <th class="small text-slate-700 uppercase fw-bold py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $groupedFiles = $files->getCollection()->groupBy('application_id');
                    @endphp

                    @forelse($groupedFiles as $appId => $docs)
                        @php 
                            $application = $docs->first()->application;
                            $applicantName = $application?->applicant_name ?: 'Unknown Applicant';
                            $ref = $application?->reference ?: 'ID: '.$appId;
                        @endphp
                        <tr class="bg-slate-50">
                            <td colspan="1" class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary-subtle text-primary rounded px-2 py-1 smaller fw-bold">{{ $ref }}</div>
                                    <span class="fw-bold text-slate-800">{{ $applicantName }}</span>
                                    <span class="badge bg-white border text-slate-700 rounded-pill smaller fw-medium">{{ $docs->count() }} files</span>
                                </div>
                            </td>
                            <td class="text-end pe-4 py-3">
                                @if($application && !auth()->user()->hasRole('it_admin'))
                                    <a href="{{ route('staff.it.application.download_batch', $application) }}" class="btn btn-sm btn-slate-200 text-slate-700 border-slate-300 fw-bold rounded-pill px-3">
                                        <i class="ri-folder-zip-line me-1"></i> Download All
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @foreach($docs as $file)
                            <tr>
                                <td class="ps-5 py-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="ri-file-3-line fs-5 text-slate-400"></i>
                                        <div>
                                            <div class="fw-bold text-slate-900 small">{{ $file->original_name ?: 'document.pdf' }}</div>
                                            <div class="smaller text-slate-600">{{ $file->document_type }} • {{ Carbon\Carbon::parse($file->created_at)->format('d M Y, H:i') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-4 py-2">
                                    @php
                                        // Attempting to get a valid URL for individual download
                                        $url = method_exists($file, 'getUrlAttribute') ? $file->url : '#';
                                        $isIt = auth()->user()->hasRole('it_admin');
                                    @endphp
                                    <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-icon text-slate-400 hover-text-primary" title="{{ $isIt ? 'View' : 'Download' }}">
                                        <i class="{{ $isIt ? 'ri-eye-line' : 'ri-download-2-line' }}"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="2" class="p-5 text-center text-slate-700 font-weight-bold">
                                <i class="ri-file-search-line fs-1 d-block mb-3 opacity-25"></i>
                                No documents matched your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $files->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h6 class="fw-bold mb-3 small uppercase text-slate-600">Disk Distribution</h6>
            <pre class="bg-dark text-success p-3 rounded-4 small mb-0"><code>{{ $storageStats['public'] }} public/
{{ $storageStats['uploads'] }} uploads/</code></pre>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-info-subtle border-0">
            <h6 class="fw-bold mb-2">Media Cleanup Tool</h6>
            <p class="small text-info-emphasis opacity-75">Automatically identify and remove orphaned files (files in storage not linked to any record).</p>
            <form action="{{ route('staff.it.system.cleanup') }}" method="POST" class="mt-auto">
                @csrf
                <button type="submit" class="btn btn-info btn-sm rounded-pill px-4 fw-bold text-white w-100">Run Scan</button>
            </form>
        </div>
    </div>
</div>
