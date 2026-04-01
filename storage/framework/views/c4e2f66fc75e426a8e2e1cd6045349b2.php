
<div class="option">
    ...
    <ul class="bullets">...</ul>

    <?php if(auth()->guard()->guest()): ?>
        <a class="btn btn-green" href="<?php echo e(route('login')); ?>">
            Login to Apply <i class="ri-arrow-right-line"></i>
        </a>
    <?php else: ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'journalist')): ?>
            <a class="btn btn-green" href="<?php echo e(route('accreditation.portal')); ?>">
                Go to My Dashboard <i class="ri-arrow-right-line"></i>
            </a>
        <?php else: ?>
            <button class="btn btn-green" style="opacity: 0.5; cursor: not-allowed;" disabled>
                Authorized for Media Practitioners Only
            </button>
        <?php endif; ?>
    <?php endif; ?>
</div>


<div class="option">
    ...
    <?php if(auth()->guard()->guest()): ?>
        <a class="btn btn-dark" href="<?php echo e(route('login')); ?>">
            Login to Register <i class="ri-arrow-right-line"></i>
        </a>
    <?php else: ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'media_house')): ?>
            <a class="btn btn-dark" href="<?php echo e(route('mediahouse.portal')); ?>">
                Go to Organization Portal <i class="ri-arrow-right-line"></i>
            </a>
        <?php else: ?>
            <button class="btn btn-dark" style="opacity: 0.5; cursor: not-allowed;" disabled>
                Authorized for Media Houses Only
            </button>
        <?php endif; ?>
    <?php endif; ?>
</div><?php /**PATH /Users/patiencemupikeni/Downloads/zmc-portalfinal-FINAL-PREVIEW-DRAFTS-REVIEWFIX-EXCL-VENDOR_1-2/resources/views/portal/index.blade.php ENDPATH**/ ?>