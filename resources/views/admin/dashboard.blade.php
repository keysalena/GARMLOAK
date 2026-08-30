@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="dashboard-header">
    <div>
        <p class="eyebrow text-green">Overview</p>
        <h1>Dashboard</h1>
    </div>
    <div class="header-actions">
        <button type="button" class="btn btn-primary" data-modal-open="{{ route('admin.modul.create') }}">+ Add Project</button>
        <button type="button" class="btn btn-secondary btn-outline">Import Data</button>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card accent-green">
        <div class="stat-top">
            <span class="stat-label">Total Projects</span>
            <span class="stat-arrow">↗</span>
        </div>
        <strong>{{ $totalModul }}</strong>
        <small>Increased from last month</small>
    </div>
    <div class="stat-card accent-light">
        <div class="stat-top">
            <span class="stat-label">Ended Projects</span>
            <span class="stat-arrow">↗</span>
        </div>
        <strong>{{ $totalChallenge }}</strong>
        <small>Increased from last month</small>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Running Projects</span>
            <span class="stat-arrow">↗</span>
        </div>
        <strong>{{ $totalKelas }}</strong>
        <small>Increased from last month</small>
    </div>
    <div class="stat-card soft-card">
        <div class="stat-top">
            <span class="stat-label">Pending Project</span>
            <span class="stat-arrow muted">↗</span>
        </div>
        <strong>{{ $totalMahasiswa }}</strong>
        <small>On Discuss</small>
    </div>
</div>

<div class="content-grid two-column">
    
</div>
@endsection