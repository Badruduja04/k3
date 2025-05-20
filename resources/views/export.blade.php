@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Export Data Dinamis</h2>
    <form method="GET" action="{{ route('export.general') }}">
        <div class="form-group mb-3">
            <label class="fw-bold">Pilih Data yang Ingin Diexport:</label><br>
            <label><input type="checkbox" name="data[]" value="users"> Users</label><br>
            <label><input type="checkbox" name="data[]" value="barang"> Barang</label><br>
            <label><input type="checkbox" name="data[]" value="lokasi"> Lokasi</label><br>
            <label><input type="checkbox" name="data[]" value="status"> Status</label><br>
            <label><input type="checkbox" name="data[]" value="pelaporan"> Pelaporan</label><br>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="month" class="form-label">Bulan</label>
                <select class="form-select" id="month" name="month">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $i, 1)->locale('id')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label for="year" class="form-label">Tahun</label>
                <select class="form-select" id="year" name="year">
                    @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Export Excel</button>
    </form>
</div>
@endsection 