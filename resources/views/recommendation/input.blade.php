@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    Input Kriteria Skincare
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-warning">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('recommendation.process') }}" method="POST">
                        @csrf

                        <!-- Tipe Kulit -->
                        <div class="mb-3">
                            <label for="skin_type" class="form-label font-weight-bold">Tipe Kulit</label>
                            <select name="skin_type" id="skin_type" class="form-select" required>
                                <option value="">-- Pilih Tipe Kulit --</option>
                                <option value="Oily">Berminyak (Oily)</option>
                                <option value="Dry">Kering (Dry)</option>
                                <option value="Combination">Kombinasi (Combination)</option>
                                <option value="Sensitive">Sensitif (Sensitive)</option>
                                <option value="Normal">Normal</option>
                            </select>
                        </div>

                        <!-- Masalah Kulit -->
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Masalah Kulit (Skin Concern)</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="skin_concern[]" value="Acne" id="acne">
                                    <label class="form-check-label" for="acne">Jerawat</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="skin_concern[]" value="Aging" id="aging">
                                    <label class="form-check-label" for="aging">Penuaan/Kerutan</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="skin_concern[]" value="Dullness" id="dullness">
                                    <label class="form-check-label" for="dullness">Kusam</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="skin_concern[]" value="Redness" id="redness">
                                    <label class="form-check-label" for="redness">Kemerahan</label>
                                </div>
                            </div>
                        </div>

                        <!-- Nilai K (Neighbors) -->
                        <div class="mb-3">
                            <label for="k" class="form-label font-weight-bold">Jumlah Tetangga Dekat (Nilai K)</label>
                            <input type="number" name="k" id="k" class="form-control" value="5" min="1" max="15">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Cari Rekomendasi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection