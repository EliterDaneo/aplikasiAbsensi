@extends('layouts.main')

@section('header')
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <i class="fas fa-arrow-left fa-2x"></i>
            </a>
        </div>
        <div class="pageTitle">E-Presensi</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
    <div class="margin-top: 70px">
        <div class="col">
            <div class="webcam-capture"></div>
        </div>
    </div>
@endsection
