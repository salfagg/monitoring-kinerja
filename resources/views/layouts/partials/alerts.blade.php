<!-- Flash Messages Component -->
@if(session('success'))
    <div class="alert-box alert-success">
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class="ph-fill ph-check-circle" style="font-size: 20px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="alert-box alert-info">
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class="ph-fill ph-info" style="font-size: 20px;"></i>
            <span>{{ session('info') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="alert-box alert-danger">
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class="ph-fill ph-warning-circle" style="font-size: 20px;"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="alert-box alert-danger">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; font-weight: 700;">
                <i class="ph-fill ph-warning-circle" style="font-size: 20px;"></i>
                <span>Terdapat kesalahan pada isian form:</span>
            </div>
            <ul style="margin-left: 28px; font-size: 13px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
