<?php $__env->startSection('title', isset($template) ? 'Edit Template' : 'Template Designer'); ?>

<?php $__env->startSection('content'); ?>
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">
        <i class="ri-palette-line me-2" style="color:var(--zmc-accent)"></i>
        <?php echo e(isset($template) ? 'Edit Template: ' . $template->name : 'Template Designer'); ?>

      </h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        Design card/certificate templates by uploading a background and placing dynamic fields.
      </div>
    </div>
    <a href="<?php echo e(route('staff.production.templates')); ?>" class="btn btn-outline-secondary btn-sm px-3">
      <i class="ri-arrow-left-line me-1"></i> Back to Templates
    </a>
  </div>

  <?php if(session('success')): ?>
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div><?php echo e(session('success')); ?></div>
    </div>
  <?php endif; ?>
  <?php if($errors->any()): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="zmc-card shadow-sm">
        <h6 class="fw-bold mb-3"><i class="ri-settings-3-line me-1"></i> Template Settings</h6>

        <form id="templateForm"
              method="POST"
              action="<?php echo e(isset($template) ? route('staff.production.templates.update', $template) : route('staff.production.templates.store')); ?>"
              enctype="multipart/form-data">
          <?php echo csrf_field(); ?>
          <?php if(isset($template)): ?>
            <?php echo method_field('PUT'); ?>
          <?php endif; ?>

          <div class="mb-3">
            <label class="form-label fw-bold small">Template Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $template->name ?? '')); ?>" required>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-bold small">Type</label>
              <select name="type" class="form-select" required>
                <option value="card" <?php echo e(old('type', $template->type ?? '') === 'card' ? 'selected' : ''); ?>>Card</option>
                <option value="certificate" <?php echo e(old('type', $template->type ?? '') === 'certificate' ? 'selected' : ''); ?>>Certificate</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label fw-bold small">Year</label>
              <input type="text" name="year" class="form-control" maxlength="4" value="<?php echo e(old('year', $template->year ?? date('Y'))); ?>" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold small">Background Image (JPG/PNG)</label>
            <input type="file" name="background" class="form-control" accept="image/jpeg,image/png" id="bgUpload">
            <?php if(isset($template) && $template->background_path): ?>
              <div class="mt-1 small text-muted">
                <i class="ri-image-line"></i> Current: <?php echo e(basename($template->background_path)); ?>

              </div>
            <?php endif; ?>
          </div>

          <input type="hidden" name="layout_config" id="layoutConfigInput" value="<?php echo e(old('layout_config', json_encode($template->layout_config ?? []))); ?>">

          <hr>

          <h6 class="fw-bold mb-3"><i class="ri-drag-move-2-line me-1"></i> Dynamic Fields</h6>
          <p class="small text-muted mb-2">Click a field to add it to the canvas. Drag fields to position them.</p>

          <div class="d-flex flex-wrap gap-2 mb-3" id="fieldPalette">
            <button type="button" class="btn btn-sm btn-outline-primary field-add-btn" data-field="name">
              <i class="ri-user-line me-1"></i> Name
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary field-add-btn" data-field="reg_number">
              <i class="ri-hashtag me-1"></i> Reg/Accred No.
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary field-add-btn" data-field="category">
              <i class="ri-price-tag-3-line me-1"></i> Category
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary field-add-btn" data-field="expiry_date">
              <i class="ri-calendar-line me-1"></i> Expiry Date
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary field-add-btn" data-field="qr_code">
              <i class="ri-qr-code-line me-1"></i> QR Code
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary field-add-btn" data-field="photo">
              <i class="ri-camera-line me-1"></i> Photo
            </button>
          </div>

          <div id="fieldProperties" class="d-none mb-3 p-3 border rounded bg-light">
            <h6 class="fw-bold small mb-2">Field Properties <span id="propFieldName" class="badge bg-primary"></span></h6>
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label small">X (px)</label>
                <input type="number" class="form-control form-control-sm" id="propX" min="0">
              </div>
              <div class="col-6">
                <label class="form-label small">Y (px)</label>
                <input type="number" class="form-control form-control-sm" id="propY" min="0">
              </div>
              <div class="col-6">
                <label class="form-label small">Width (px)</label>
                <input type="number" class="form-control form-control-sm" id="propW" min="20">
              </div>
              <div class="col-6">
                <label class="form-label small">Height (px)</label>
                <input type="number" class="form-control form-control-sm" id="propH" min="15">
              </div>
              <div class="col-6">
                <label class="form-label small">Font Size</label>
                <input type="number" class="form-control form-control-sm" id="propFontSize" min="8" max="72" value="14">
              </div>
              <div class="col-6">
                <label class="form-label small">Color</label>
                <input type="color" class="form-control form-control-sm form-control-color" id="propColor" value="#000000">
              </div>
            </div>
            <div class="mt-2 d-flex gap-2">
              <button type="button" class="btn btn-sm btn-success" id="propApply">Apply</button>
              <button type="button" class="btn btn-sm btn-outline-danger" id="propRemove">Remove Field</button>
            </div>
          </div>

          <hr>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary fw-bold flex-fill" onclick="prepareSubmit()">
              <i class="ri-save-line me-1"></i> <?php echo e(isset($template) ? 'Update' : 'Save'); ?> Template
            </button>
            <button type="button" class="btn btn-outline-info fw-bold" onclick="togglePreview()">
              <i class="ri-eye-line me-1"></i> Preview
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="zmc-card shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold m-0"><i class="ri-artboard-2-line me-1"></i> Canvas</h6>
          <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-light text-dark border" id="canvasSize">850 × 540</span>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearCanvas()">
              <i class="ri-delete-bin-line me-1"></i> Clear All
            </button>
          </div>
        </div>

        <div id="canvasWrapper" style="position:relative; width:850px; max-width:100%; height:540px; border:2px dashed #cbd5e1; border-radius:8px; overflow:hidden; background:#f8fafc; margin:0 auto;">
          <div id="canvasBg" style="position:absolute; top:0; left:0; width:100%; height:100%; background-size:cover; background-position:center; background-repeat:no-repeat;"></div>
          <div id="canvasFields" style="position:absolute; top:0; left:0; width:100%; height:100%;"></div>
          <div id="canvasEmpty" style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; color:#94a3b8;">
            <i class="ri-image-add-line" style="font-size:48px;"></i>
            <div class="mt-2">Upload a background image and add fields</div>
          </div>
        </div>
      </div>

      <div class="zmc-card shadow-sm mt-4 d-none" id="previewCard">
        <h6 class="fw-bold mb-3"><i class="ri-eye-line me-1"></i> Preview with Sample Data</h6>
        <div id="previewContainer" style="position:relative; width:850px; max-width:100%; height:540px; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; margin:0 auto;">
        </div>
      </div>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const SAMPLE_DATA = {
  name: 'John M. Doe',
  reg_number: 'ZMC/ACC/2025/001234',
  category: 'Print Media',
  expiry_date: '31 Dec 2025',
  qr_code: 'QR',
  photo: 'Photo'
};

const FIELD_LABELS = {
  name: 'Name',
  reg_number: 'Reg/Accred No.',
  category: 'Category',
  expiry_date: 'Expiry Date',
  qr_code: 'QR Code',
  photo: 'Photo'
};

const FIELD_DEFAULTS = {
  name:        { x: 250, y: 180, w: 300, h: 30, fontSize: 18, color: '#000000' },
  reg_number:  { x: 250, y: 220, w: 300, h: 25, fontSize: 14, color: '#333333' },
  category:    { x: 250, y: 260, w: 200, h: 25, fontSize: 14, color: '#333333' },
  expiry_date: { x: 250, y: 300, w: 200, h: 25, fontSize: 14, color: '#333333' },
  qr_code:     { x: 680, y: 350, w: 120, h: 120, fontSize: 12, color: '#000000' },
  photo:       { x: 50,  y: 150, w: 150, h: 180, fontSize: 12, color: '#000000' }
};

let fields = {};
let selectedField = null;
let dragState = null;

function initFromExisting() {
  try {
    const raw = document.getElementById('layoutConfigInput').value;
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed)) {
      parsed.forEach(f => {
        if (f.key) {
          fields[f.key] = { x: f.x||0, y: f.y||0, w: f.w||100, h: f.h||30, fontSize: f.fontSize||14, color: f.color||'#000000' };
        }
      });
    }
  } catch(e) {}
  renderFields();
}

<?php if(isset($template) && $template->background_path): ?>
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('canvasBg').style.backgroundImage = 'url(<?php echo e(asset("storage/" . $template->background_path)); ?>)';
    document.getElementById('canvasEmpty').style.display = 'none';
  });
<?php endif; ?>

document.getElementById('bgUpload').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function(ev) {
    document.getElementById('canvasBg').style.backgroundImage = 'url(' + ev.target.result + ')';
    document.getElementById('canvasEmpty').style.display = 'none';
  };
  reader.readAsDataURL(file);
});

document.querySelectorAll('.field-add-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const key = this.dataset.field;
    if (fields[key]) {
      selectField(key);
      return;
    }
    const defaults = FIELD_DEFAULTS[key] || { x:100, y:100, w:150, h:30, fontSize:14, color:'#000000' };
    fields[key] = { ...defaults };
    renderFields();
    selectField(key);
  });
});

function renderFields() {
  const container = document.getElementById('canvasFields');
  container.innerHTML = '';
  Object.keys(fields).forEach(key => {
    const f = fields[key];
    const el = document.createElement('div');
    el.className = 'designer-field' + (selectedField === key ? ' selected' : '');
    el.dataset.key = key;
    el.style.cssText = `position:absolute; left:${f.x}px; top:${f.y}px; width:${f.w}px; height:${f.h}px; border:2px solid ${selectedField===key?'#2563eb':'#64748b'}; background:rgba(255,255,255,0.85); cursor:move; display:flex; align-items:center; justify-content:center; font-size:${f.fontSize}px; color:${f.color}; border-radius:4px; user-select:none; z-index:10;`;

    const isSpecial = key === 'qr_code' || key === 'photo';
    if (isSpecial) {
      el.innerHTML = '<div style="text-align:center;font-size:11px;color:#64748b;"><i class="ri-' + (key==='qr_code'?'qr-code':'camera') + '-line" style="font-size:24px;display:block;"></i>' + FIELD_LABELS[key] + '</div>';
    } else {
      el.textContent = FIELD_LABELS[key];
    }

    el.addEventListener('mousedown', function(e) {
      e.preventDefault();
      selectField(key);
      dragState = { key, startX: e.clientX, startY: e.clientY, origX: f.x, origY: f.y };
    });

    container.appendChild(el);
  });
}

document.addEventListener('mousemove', function(e) {
  if (!dragState) return;
  const dx = e.clientX - dragState.startX;
  const dy = e.clientY - dragState.startY;
  fields[dragState.key].x = Math.max(0, dragState.origX + dx);
  fields[dragState.key].y = Math.max(0, dragState.origY + dy);
  renderFields();
  updateProperties();
});

document.addEventListener('mouseup', function() {
  dragState = null;
});

function selectField(key) {
  selectedField = key;
  renderFields();
  showProperties(key);
}

function showProperties(key) {
  const panel = document.getElementById('fieldProperties');
  const f = fields[key];
  if (!f) { panel.classList.add('d-none'); return; }
  panel.classList.remove('d-none');
  document.getElementById('propFieldName').textContent = FIELD_LABELS[key] || key;
  document.getElementById('propX').value = Math.round(f.x);
  document.getElementById('propY').value = Math.round(f.y);
  document.getElementById('propW').value = f.w;
  document.getElementById('propH').value = f.h;
  document.getElementById('propFontSize').value = f.fontSize;
  document.getElementById('propColor').value = f.color;
}

function updateProperties() {
  if (!selectedField || !fields[selectedField]) return;
  const f = fields[selectedField];
  document.getElementById('propX').value = Math.round(f.x);
  document.getElementById('propY').value = Math.round(f.y);
}

document.getElementById('propApply').addEventListener('click', function() {
  if (!selectedField || !fields[selectedField]) return;
  fields[selectedField].x = parseInt(document.getElementById('propX').value) || 0;
  fields[selectedField].y = parseInt(document.getElementById('propY').value) || 0;
  fields[selectedField].w = parseInt(document.getElementById('propW').value) || 100;
  fields[selectedField].h = parseInt(document.getElementById('propH').value) || 30;
  fields[selectedField].fontSize = parseInt(document.getElementById('propFontSize').value) || 14;
  fields[selectedField].color = document.getElementById('propColor').value || '#000000';
  renderFields();
});

document.getElementById('propRemove').addEventListener('click', function() {
  if (!selectedField) return;
  delete fields[selectedField];
  selectedField = null;
  document.getElementById('fieldProperties').classList.add('d-none');
  renderFields();
});

function clearCanvas() {
  if (!confirm('Remove all fields from the canvas?')) return;
  fields = {};
  selectedField = null;
  document.getElementById('fieldProperties').classList.add('d-none');
  renderFields();
}

function prepareSubmit() {
  const config = Object.keys(fields).map(key => ({
    key,
    label: FIELD_LABELS[key] || key,
    ...fields[key]
  }));
  document.getElementById('layoutConfigInput').value = JSON.stringify(config);
}

function togglePreview() {
  const card = document.getElementById('previewCard');
  card.classList.toggle('d-none');
  if (!card.classList.contains('d-none')) {
    renderPreview();
  }
}

function renderPreview() {
  const container = document.getElementById('previewContainer');
  const bgStyle = document.getElementById('canvasBg').style.backgroundImage;
  let html = '<div style="position:absolute;top:0;left:0;width:100%;height:100%;background-size:cover;background-position:center;' + (bgStyle ? 'background-image:' + bgStyle + ';' : 'background:#f1f5f9;') + '"></div>';

  Object.keys(fields).forEach(key => {
    const f = fields[key];
    const val = SAMPLE_DATA[key] || key;
    const isSpecial = key === 'qr_code' || key === 'photo';

    if (key === 'qr_code') {
      html += `<div style="position:absolute;left:${f.x}px;top:${f.y}px;width:${f.w}px;height:${f.h}px;display:flex;align-items:center;justify-content:center;background:white;border:1px solid #ccc;border-radius:4px;"><i class="ri-qr-code-line" style="font-size:${Math.min(f.w,f.h)*0.7}px;color:#333;"></i></div>`;
    } else if (key === 'photo') {
      html += `<div style="position:absolute;left:${f.x}px;top:${f.y}px;width:${f.w}px;height:${f.h}px;display:flex;align-items:center;justify-content:center;background:#e2e8f0;border:2px solid #94a3b8;border-radius:4px;"><i class="ri-user-line" style="font-size:${Math.min(f.w,f.h)*0.5}px;color:#64748b;"></i></div>`;
    } else {
      html += `<div style="position:absolute;left:${f.x}px;top:${f.y}px;width:${f.w}px;height:${f.h}px;display:flex;align-items:center;font-size:${f.fontSize}px;color:${f.color};font-weight:600;">${val}</div>`;
    }
  });

  container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', function() {
  initFromExisting();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/ZMCPORTAL/resources/views/staff/production/designer.blade.php ENDPATH**/ ?>