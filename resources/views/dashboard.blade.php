@extends('template.layout')

@section('title')
    Dashboard - ZeroPus
@endsection

@section('header_title')
    Dashboard
@endsection

@section('content')
<!-- Card Count -->
<div class="row">
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            USER</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-solid fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            CATEGORY</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $category }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-fw fa-folder fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            BOOK</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $book }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-fw fa-folder fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            ROLE</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $role }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-fw fa-folder fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    
@endsection