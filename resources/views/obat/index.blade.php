@extends('layouts.app')

@section('title', 'Daftar Obat')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Daftar Obat</h1>
            <div>
                <button type="button" class="btn btn-success" id="btn-export-excel">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </button>
                <button type="button" class="btn btn-danger" id="btn-export-pdf">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </button>
                <button type="button" class="btn btn-primary" id="btn-add-obat">
                    <i class="bi bi-plus-lg"></i> Tambah Obat
                </button>
            </div>
        </div>

        <div id="page-alert"></div>

        <div class="card">
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <input type="text" class="form-control" id="search" placeholder="Cari nama, kode, atau kategori obat...">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="filter-category">
                            <option value="">Semua Kategori</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filter-stock-status">
                            <option value="">Semua Status Stok</option>
                            <option value="available">Tersedia</option>
                            <option value="low">Stok Menipis</option>
                            <option value="out">Habis</option>
                            <option value="expired">Kadaluarsa</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Obat</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Kadaluarsa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="obat-table-body">
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"
                                        aria-hidden="true"></span>
                                    Memuat data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                    <div id="table-info" class="text-muted small"></div>
                    <nav id="pagination-area" aria-label="Pagination" style="max-width: 100%; overflow-x: auto;">
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Obat -->
    <div class="modal fade" id="modal-add-obat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Obat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-add-obat">
                    <div class="modal-body">
                        <div id="modal-alert"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode *</label>
                                <input type="text" name="code" class="form-control" required>
                                <div class="invalid-feedback" data-error="code"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Obat *</label>
                                <input type="text" name="name" class="form-control" required>
                                <div class="invalid-feedback" data-error="name"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori *</label>
                                <input type="text" name="category" class="form-control" required
                                    placeholder="cth: Antibiotik">
                                <div class="invalid-feedback" data-error="category"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Satuan *</label>
                                <input type="text" name="unit" class="form-control" required
                                    placeholder="cth: Tablet / Botol / Strip">
                                <div class="invalid-feedback" data-error="unit"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga Beli *</label>
                                <input type="number" name="purchase_price" class="form-control" min="0"
                                    value="0" required>
                                <div class="invalid-feedback" data-error="purchase_price"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga Jual *</label>
                                <input type="number" name="selling_price" class="form-control" min="0"
                                    value="0" required>
                                <div class="invalid-feedback" data-error="selling_price"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stok *</label>
                                <input type="number" name="stock" class="form-control" min="0" value="0"
                                    required>
                                <div class="invalid-feedback" data-error="stock"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stok Minimum *</label>
                                <input type="number" name="minimum_stock" class="form-control" min="0"
                                    value="0" required>
                                <div class="invalid-feedback" data-error="minimum_stock"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Kadaluarsa</label>
                                <input type="date" name="expired_date" class="form-control">
                                <div class="invalid-feedback" data-error="expired_date"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" class="form-control" rows="1"></textarea>
                                <div class="invalid-feedback" data-error="notes"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-obat">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Obat -->
    <div class="modal fade" id="modal-edit-obat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Obat</h5>
                    <span id="autosave-status" class="badge bg-secondary ms-2">Idle</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-edit-obat">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="modal-body">
                        <div id="modal-edit-alert"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode *</label>
                                <input type="text" name="code" id="edit-code" class="form-control" required>
                                <div class="invalid-feedback" data-error="code"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Obat *</label>
                                <input type="text" name="name" id="edit-name" class="form-control" required>
                                <div class="invalid-feedback" data-error="name"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori *</label>
                                <input type="text" name="category" id="edit-category" class="form-control" required>
                                <div class="invalid-feedback" data-error="category"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Satuan *</label>
                                <input type="text" name="unit" id="edit-unit" class="form-control" required>
                                <div class="invalid-feedback" data-error="unit"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga Beli *</label>
                                <input type="number" name="purchase_price" id="edit-purchase_price"
                                    class="form-control" min="0" required>
                                <div class="invalid-feedback" data-error="purchase_price"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga Jual *</label>
                                <input type="number" name="selling_price" id="edit-selling_price" class="form-control"
                                    min="0" required>
                                <div class="invalid-feedback" data-error="selling_price"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stok * <span class="badge bg-info ms-1">Auto-save</span></label>
                                <input type="number" name="stock" id="edit-stock" class="form-control autosave-field"
                                    min="0" required>
                                <div class="invalid-feedback" data-error="stock"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stok Minimum * <span
                                        class="badge bg-info ms-1">Auto-save</span></label>
                                <input type="number" name="minimum_stock" id="edit-minimum_stock"
                                    class="form-control autosave-field" min="0" required>
                                <div class="invalid-feedback" data-error="minimum_stock"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Kadaluarsa <span
                                        class="badge bg-info ms-1">Auto-save</span></label>
                                <input type="date" name="expired_date" id="edit-expired_date"
                                    class="form-control autosave-field">
                                <div class="invalid-feedback" data-error="expired_date"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catatan <span
                                        class="badge bg-info ms-1">Auto-save</span></label>
                                <textarea name="notes" id="edit-notes" class="form-control autosave-field" rows="1"></textarea>
                                <div class="invalid-feedback" data-error="notes"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <span class="text-muted small me-auto">4 field bertanda Auto-save tersimpan otomatis.
                            Field lain pakai tombol Update.</span>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning" id="btn-update-obat">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Obat -->
    <div class="modal fade" id="modal-delete-obat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Obat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin hapus <strong id="delete-obat-name"></strong>?</p>
                    <p class="text-muted small mb-0">Data yang dihapus tidak bisa dikembalikan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btn-confirm-delete">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Obat (read-only) -->
    <div class="modal fade" id="modal-detail-obat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Obat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="detail-alert"></div>
                    <dl class="row mb-0" id="detail-content">
                        <dt class="col-sm-4">Kode</dt>
                        <dd class="col-sm-8" id="detail-code">-</dd>
                        <dt class="col-sm-4">Nama</dt>
                        <dd class="col-sm-8" id="detail-name">-</dd>
                        <dt class="col-sm-4">Kategori</dt>
                        <dd class="col-sm-8" id="detail-category">-</dd>
                        <dt class="col-sm-4">Satuan</dt>
                        <dd class="col-sm-8" id="detail-unit">-</dd>
                        <dt class="col-sm-4">Harga Beli</dt>
                        <dd class="col-sm-8" id="detail-purchase_price">-</dd>
                        <dt class="col-sm-4">Harga Jual</dt>
                        <dd class="col-sm-8" id="detail-selling_price">-</dd>
                        <dt class="col-sm-4">Stok</dt>
                        <dd class="col-sm-8" id="detail-stock">-</dd>
                        <dt class="col-sm-4">Min. Stok</dt>
                        <dd class="col-sm-8" id="detail-minimum_stock">-</dd>
                        <dt class="col-sm-4">Kadaluarsa</dt>
                        <dd class="col-sm-8" id="detail-expired_date">-</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8" id="detail-status">-</dd>
                        <dt class="col-sm-4">Catatan</dt>
                        <dd class="col-sm-8" id="detail-notes">-</dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Export (dipakai Excel & PDF, tampilan sama) -->
    <div class="modal fade" id="modal-export" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-export-title">Export Data Obat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="export-status-area"></div>
                    <div class="d-flex justify-content-between mb-2">
                        <span id="export-progress-label" class="fw-bold">0%</span>
                        <span id="export-progress-count" class="text-muted small">0 / 0 data</span>
                    </div>
                    <div class="progress" style="height: 24px;">
                        <div id="export-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: 0%;">0%</div>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Progress ini data asli dari server, bukan animasi.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" id="btn-download-export" class="btn btn-success d-none">Download</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Variabel untuk menyimpan halaman aktif & timer search
            let currentPage = 1;
            let searchTimer = null;

            // Pertama kali halaman dibuka -> langsung ambil data halaman 1
            loadData(1);

            // Kalau user mengetik di search, tunggu 500ms baru request
            // supaya tidak request tiap 1 huruf
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    loadData(1);
                }, 500);
            });

            // Kalau dropdown kategori / status berubah -> reload dari hal. 1
            $('#filter-category, #filter-stock-status').on('change', function() {
                loadData(1);
            });

            // Klik tombol pagination (dibuat dinamis, jadi pakai .on)
            $('#pagination-area').on('click', '.page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page) loadData(page);
            });

            // ===== CREATE: buka modal tambah =====
            $('#btn-add-obat').on('click', function() {
                // Bersihkan form & error lama, lalu tampilkan modal
                $('#form-add-obat')[0].reset();
                $('#form-add-obat .is-invalid').removeClass('is-invalid');
                $('#modal-alert').html('');
                $('#modal-add-obat').modal('show');
            });

            // ===== CREATE: kirim form tambah via AJAX =====
            $('#form-add-obat').on('submit', function(e) {
                e.preventDefault(); // cegah reload halaman

                // Hilangkan error lama
                $('#form-add-obat .is-invalid').removeClass('is-invalid');
                $('#modal-alert').html('');

                $.ajax({
                    url: "{{ route('obat.store') }}",
                    type: "POST",
                    data: $(this).serialize(), // ambil semua input form sekaligus
                    headers: {
                        // Wajib untuk POST di Laravel (diambil dari meta di layouts/app.blade.php)
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#btn-save-obat').prop('disabled', true).text('Menyimpan...');
                    },
                    success: function(res) {
                        $('#modal-add-obat').modal('hide');
                        // Tampilkan pesan sukses di atas tabel
                        $('#page-alert').html(
                            '<div class="alert alert-success alert-dismissible fade show">' +
                            res.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                        );
                        loadData(1); // refresh tabel ke halaman 1
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // 422 = validasi Laravel gagal.
                            // xhr.responseJSON.errors berisi { "code": ["pesan..."], ... }
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(field) {
                                let input = $('#form-add-obat [name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.siblings('[data-error="' + field + '"]').text(
                                    errors[field][0]);
                            });
                        } else {
                            $('#modal-alert').html(
                                '<div class="alert alert-danger">Gagal menyimpan data. Coba lagi.</div>'
                            );
                        }
                    },
                    complete: function() {
                        $('#btn-save-obat').prop('disabled', false).text('Simpan');
                    }
                });
            });

            // ===== FUNGSI UTAMA =====
            function loadData(page) {
                currentPage = page;

                $.ajax({
                    url: "{{ route('obat.data') }}", // alamat API JSON
                    type: "GET",
                    data: {
                        page: page,
                        search: $('#search').val(),
                        category: $('#filter-category').val(),
                        stock_status: $('#filter-stock-status').val()
                    },
                    beforeSend: function() {
                        // Tampilkan loading saat request berjalan
                        $('#obat-table-body').html(
                            '<tr><td colspan="10" class="text-center text-muted py-4">' +
                            '<span class="spinner-border spinner-border-sm me-2"></span>Memuat data...' +
                            '</td></tr>'
                        );
                    },
                    success: function(res) {
                        // 1. Isi dropdown kategori (hanya sekali saja biar pilihan user tidak hilang)
                        if ($('#filter-category option').length <= 1 && res.categories) {
                            res.categories.forEach(function(cat) {
                                $('#filter-category').append(
                                    '<option value="' + escapeHtml(cat) + '">' + escapeHtml(cat) + '</option>'
                                );
                            });
                        }

                        // 2. Kalau data kosong
                        if (res.data.length === 0) {
                            $('#obat-table-body').html(
                                '<tr><td colspan="10" class="text-center text-muted py-4">Data tidak ditemukan</td></tr>'
                            );
                        } else {

                            // 3. Loop tiap obat -> buat baris <tr>
                            // Semua teks dari server lewat escapeHtml() dulu anti-XSS
                            let rows = '';
                            res.data.forEach(function(obat) {
                                rows += '<tr>' +
                                    '<td>' + escapeHtml(obat.code) + '</td>' +
                                    '<td>' + escapeHtml(obat.name) + '</td>' +
                                    '<td>' + escapeHtml(obat.category) + '</td>' +
                                    '<td>' + escapeHtml(obat.unit) + '</td>' +
                                    '<td>Rp' + Number(obat.purchase_price).toLocaleString(
                                    'id-ID') + '</td>' +
                                    '<td>Rp' + Number(obat.selling_price).toLocaleString(
                                    'id-ID') + '</td>' +
                                    '<td>' + obat.stock + '</td>' +
                                    '<td>' + statusBadge(obat) + '</td>' +
                                    '<td>' + escapeHtml(formatTanggal(obat.expired_date)) + '</td>' +
                                    '<td class="text-nowrap">' +
                                    '<button type="button" class="btn btn-sm btn-info btn-detail" data-id="' +
                                    obat.id + '">Lihat</button> ' +
                                    '<button type="button" class="btn btn-sm btn-warning btn-edit" data-id="' +
                                    obat.id + '">Edit</button> ' +
                                    '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' +
                                    obat.id + '" data-name="' + escapeHtml(obat.name) +
                                    '">Hapus</button>' +
                                    '</td>' +
                                    '</tr>';
                            });
                            $('#obat-table-body').html(rows);
                        }

                        // 4. Info "Menampilkan 1-10 dari 50 data"
                        let p = res.pagination;
                        $('#table-info').text(
                            p.total === 0 ? 'Tidak ada data' :
                            'Menampilkan ' + (p.from ?? 0) + ' - ' + (p.to ?? 0) + ' dari ' + p
                            .total + ' data'
                        );

                        // 5. Gambar tombol pagination
                        renderPagination(p.current_page, p.last_page);
                    },
                    error: function() {
                        $('#obat-table-body').html(
                            '<tr><td colspan="10" class="text-center text-danger py-4">Gagal memuat data</td></tr>'
                        );
                    }
                });
            }

            // Tampilkan yyyy-mm-dd dari server sebagai dd/mm/yyyy.
            // Input date tetap pakai yyyy-mm-dd (syarat browser), cuma tampilan yang diubah.
            function formatTanggal(iso) {
                if (!iso) return '-';
                let p = String(iso).split('-');
                if (p.length !== 3) return iso;
                return p[2] + '/' + p[1] + '/' + p[0];
            }

            // Anti-XSS: ubah < > & " ' jadi teks biasa sebelum ditempel ke HTML.
            // Wajib dipakai tiap menampilkan data dari server (nama, kode, dll).
            function escapeHtml(text) {
                if (text === null || text === undefined) return '';
                return String(text)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // Tentukan label status dari stok & tanggal kadaluarsa
            function statusBadge(obat) {
                // Tanggal lokal (WIB) dari browser, bukan UTC — supaya tidak selisih sehari.
                let d = new Date();
                let today = d.getFullYear() + '-' +
                    String(d.getMonth() + 1).padStart(2, '0') + '-' +
                    String(d.getDate()).padStart(2, '0');
                if (obat.expired_date && obat.expired_date < today)
                    return '<span class="badge bg-dark">Stok Kadaluarsa</span>';
                if (obat.stock <= 0)
                    return '<span class="badge bg-danger">Stok Habis</span>';
                if (obat.stock <= obat.minimum_stock)
                    return '<span class="badge bg-warning">Stok Menipis</span>';
                return '<span class="badge bg-success">Tersedia</span>';
            }

            // Buat tombol Prev / angka / Next (ringkas: 1 ... 4 5 [6] 7 8 ... 1001)
            // Kalau semua halaman digambar (1.001 tombol), pasti meluber keluar tabel.
            function renderPagination(current, last) {
                if (last <= 1) {
                    $('#pagination-area').html('');
                    return;
                }

                // 1. Tentukan angka mana saja yang ditampilkan
                let pages = [];
                if (last <= 7) {
                    for (let i = 1; i <= last; i++) pages.push(i);
                } else {
                    pages.push(1);
                    if (current > 4) pages.push('...');
                    for (let i = Math.max(2, current - 2); i <= Math.min(last - 1, current + 2); i++) {
                        pages.push(i);
                    }
                    if (current < last - 3) pages.push('...');
                    pages.push(last);
                }

                // 2. Gambar tombolnya
                let html = '<ul class="pagination mb-0 flex-wrap">';
                html += '<li class="page-item ' + (current === 1 ? 'disabled' : '') + '">' +
                    '<a class="page-link" href="#" data-page="' + (current - 1) + '">‹</a></li>';
                pages.forEach(function(p) {
                    if (p === '...') {
                        html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
                    } else {
                        html += '<li class="page-item ' + (p === current ? 'active' : '') + '">' +
                            '<a class="page-link" href="#" data-page="' + p + '">' + p + '</a></li>';
                    }
                });
                html += '<li class="page-item ' + (current === last ? 'disabled' : '') + '">' +
                    '<a class="page-link" href="#" data-page="' + (current + 1) + '">›</a></li>';
                html += '</ul>';
                $('#pagination-area').html(html);
            }

            // ===== EDIT: tombol edit di tiap baris (pakai delegasi karena baris dinamis) =====
            let editId = null;

            // --- Variabel khusus auto-save ---
            let isFilling = false; // true saat kita isi form via kode (bukan ketikan user)
            let autosaveTimer = null; // untuk debounce
            let autosaveSeq = 0; // nomor urut request, untuk cegah race condition
            let pendingAutosave = null; // menyimpan request AJAX yang sedang jalan

            function setAutosaveStatus(state, text) {
                let badge = $('#autosave-status');
                badge.removeClass('bg-secondary bg-warning bg-success bg-danger');
                if (state === 'saving') badge.addClass('bg-warning').text(text || 'Saving...');
                else if (state === 'saved') badge.addClass('bg-success').text(text || 'Saved');
                else if (state === 'failed') badge.addClass('bg-danger').text(text || 'Failed to save');
                else badge.addClass('bg-secondary').text(text || 'Idle');
            }

            // ===== DETAIL: tombol lihat di tiap baris (read-only) =====
            $('#obat-table-body').on('click', '.btn-detail', function() {
                let id = $(this).data('id');
                $('#detail-alert').html('');

                $.ajax({
                    url: "/obat/" + id,
                    type: "GET",
                    success: function(res) {
                        let obat = res.data;
                        $('#detail-code').text(obat.code);
                        $('#detail-name').text(obat.name);
                        $('#detail-category').text(obat.category);
                        $('#detail-unit').text(obat.unit);
                        $('#detail-purchase_price').text('Rp' + Number(obat.purchase_price).toLocaleString('id-ID'));
                        $('#detail-selling_price').text('Rp' + Number(obat.selling_price).toLocaleString('id-ID'));
                        $('#detail-stock').text(obat.stock);
                        $('#detail-minimum_stock').text(obat.minimum_stock);
                        $('#detail-expired_date').text(formatTanggal(obat.expired_date));
                        $('#detail-status').html(statusBadge(obat));
                        $('#detail-notes').text(obat.notes ?? '-');
                        $('#modal-detail-obat').modal('show');
                    },
                    error: function(xhr) {
                        let msg = xhr.status === 404
                            ? 'Data tidak ditemukan (mungkin sudah dihapus). Tabel akan di-refresh.'
                            : 'Gagal mengambil data. Coba lagi.';
                        $('#page-alert').html(
                            '<div class="alert alert-danger alert-dismissible fade show">' +
                            msg +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                        );
                        loadData(currentPage);
                    }
                });
            });

            $('#obat-table-body').on('click', '.btn-edit', function() {
                editId = $(this).data('id');
                $('#form-edit-obat .is-invalid').removeClass('is-invalid');
                $('#modal-edit-alert').html('');

                // Reset auto-save setiap buka modal baru
                clearTimeout(autosaveTimer);
                if (pendingAutosave) pendingAutosave.abort();
                autosaveSeq++; // anggap request lama sudah basi
                setAutosaveStatus('idle');
                isFilling = true; // kunci: pengisian di bawah jangan picu auto-save

                // 1. Ambil dulu data lama dari server, baru isi ke form
                $.ajax({
                    url: "/obat/" + editId + "/edit",
                    type: "GET",
                    success: function(res) {
                        let obat = res.data;
                        $('#edit-id').val(obat.id);
                        $('#edit-code').val(obat.code);
                        $('#edit-name').val(obat.name);
                        $('#edit-category').val(obat.category);
                        $('#edit-unit').val(obat.unit);
                        $('#edit-purchase_price').val(parseFloat(obat.purchase_price));
                        $('#edit-selling_price').val(parseFloat(obat.selling_price));
                        $('#edit-stock').val(obat.stock);
                        $('#edit-minimum_stock').val(obat.minimum_stock);
                        $('#edit-expired_date').val(obat.expired_date);
                        $('#edit-notes').val(obat.notes);
                        $('#modal-edit-obat').modal('show');
                    },
                    error: function(xhr) {
                        // Misal datanya keburu dihapus user lain / ID tidak ada
                        let msg = xhr.status === 404
                            ? 'Data tidak ditemukan (mungkin sudah dihapus). Tabel akan di-refresh.'
                            : 'Gagal mengambil data. Coba lagi.';
                        $('#page-alert').html(
                            '<div class="alert alert-danger alert-dismissible fade show">' +
                            msg +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                        );
                        loadData(currentPage);
                    },
                    complete: function() {
                        isFilling =
                        false; // buka kunci: setelah ini ketikan user boleh picu auto-save
                    }
                });
            });

            // ===== AUTO-SAVE: hanya untuk 4 field =====
            // input = tiap ketik, change = tiap ganti tanggal / selesai pilih angka
            $('#edit-stock, #edit-minimum_stock, #edit-expired_date, #edit-notes').on('input change', function() {
                if (isFilling || !editId) return; // abaikan pengisian otomatis saat buka modal
                scheduleAutosave();
            });

            function scheduleAutosave() {
                clearTimeout(autosaveTimer); // batalkan jadwal lama
                // Tunggu user berhenti mengetik 800ms, baru kirim.
                // Jadi ngetik "paracetamol" tidak kirim 11x, cuma 1x.
                autosaveTimer = setTimeout(doAutosave, 800);
            }

            function doAutosave() {
                if (!editId || isFilling) return;

                autosaveSeq++; // request baru dapat nomor lebih besar
                let mySeq = autosaveSeq; // simpan nomor milik request ini

                // Batalkan request sebelumnya yang belum selesai (hemat server)
                if (pendingAutosave) pendingAutosave.abort();

                setAutosaveStatus('saving');

                pendingAutosave = $.ajax({
                    url: "/obat/" + editId,
                    type: "POST",
                    data: $('#form-edit-obat').serialize() + "&_method=PUT",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        // Kalau sudah ada request lebih baru, abaikan response lama ini
                        if (mySeq !== autosaveSeq) return;
                        let time = new Date().toLocaleTimeString('id-ID');
                        setAutosaveStatus('saved', 'Saved ' + time);
                        // Bersihkan error lama di 4 field itu
                        $('#edit-stock, #edit-minimum_stock, #edit-expired_date, #edit-notes')
                            .removeClass('is-invalid');
                    },
                    error: function(xhr) {
                        if (xhr.statusText === 'abort') return; // dibatalkan sengaja, bukan gagal
                        if (mySeq !== autosaveSeq) return; // response basi, abaikan
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(field) {
                                let input = $('#form-edit-obat [name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.siblings('[data-error="' + field + '"]').text(
                                    errors[field][0]);
                            });
                            setAutosaveStatus('failed', 'Failed to save');
                        } else {
                            setAutosaveStatus('failed', 'Failed to save');
                        }
                    },
                    complete: function() {
                        if (mySeq === autosaveSeq) pendingAutosave = null;
                    }
                });
            }

            // Saat modal ditutup, rapikan timer/request lalu refresh tabel diam-diam
            $('#modal-edit-obat').on('hidden.bs.modal', function() {
                clearTimeout(autosaveTimer);
                if (pendingAutosave) pendingAutosave.abort();
                if (editId) loadData(currentPage);
            });

            // ===== EDIT manual: tombol Update untuk field lain (selain 3 auto-save) =====
            $('#form-edit-obat').on('submit', function(e) {
                e.preventDefault();
                // Matikan auto-save yang mungkin sedang antre/jalan supaya tidak balapan
                clearTimeout(autosaveTimer);
                if (pendingAutosave) pendingAutosave.abort();
                autosaveSeq++;
                $('#form-edit-obat .is-invalid').removeClass('is-invalid');
                $('#modal-edit-alert').html('');

                $.ajax({
                    url: "/obat/" + editId,
                    type: "POST", // trik Laravel: kirim POST + _method PUT
                    data: $(this).serialize() + "&_method=PUT",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#btn-update-obat').prop('disabled', true).text('Menyimpan...');
                    },
                    success: function(res) {
                        $('#modal-edit-obat').modal('hide');
                        $('#page-alert').html(
                            '<div class="alert alert-success alert-dismissible fade show">' +
                            res.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                        );
                        loadData(currentPage); // tetap di halaman yang sama
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(field) {
                                let input = $('#form-edit-obat [name="' + field +
                                    '"]');
                                input.addClass('is-invalid');
                                input.siblings('[data-error="' + field + '"]')
                                    .text(errors[field][0]);
                            });
                        } else {
                            $('#modal-edit-alert').html(
                                '<div class="alert alert-danger">Gagal update data.</div>'
                            );
                        }
                    },
                    complete: function() {
                        $('#btn-update-obat').prop('disabled', false).text('Update');
                    }
                });
            });

            // ===== DELETE: buka modal konfirmasi =====
            let deleteId = null;
            $('#obat-table-body').on('click', '.btn-delete', function() {
                deleteId = $(this).data('id');
                $('#delete-obat-name').text($(this).data('name'));
                $('#modal-delete-obat').modal('show');
            });

            // ===== DELETE: eksekusi hapus =====
            $('#btn-confirm-delete').on('click', function() {
                $.ajax({
                    url: "/obat/" + deleteId,
                    type: "POST", // trik Laravel: POST + _method DELETE
                    data: {
                        _method: "DELETE"
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#btn-confirm-delete').prop('disabled', true).text('Menghapus...');
                    },
                    success: function(res) {
                        $('#modal-delete-obat').modal('hide');
                        $('#page-alert').html(
                            '<div class="alert alert-success alert-dismissible fade show">' +
                            res.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                        );
                        loadData(currentPage);
                    },
                    complete: function() {
                        $('#btn-confirm-delete').prop('disabled', false).text('Ya, Hapus');
                    }
                });
            });

            // ===== EXPORT (Excel & PDF, tampilan sama, polling sama) =====
            let exportPollTimer = null;
            let exportConfig = null; // menyimpan startUrl/statusBase/downloadBase aktif

            function resetExportUI() {
                clearInterval(exportPollTimer);
                $('#export-progress-bar').css('width', '0%').text('0%')
                    .removeClass('bg-danger bg-success').addClass('bg-primary');
                $('#export-progress-label').text('0%');
                $('#export-progress-count').text('0 / 0 data');
                $('#export-status-area').html('');
                $('#btn-download-export').addClass('d-none').attr('href', '#').text('Download');
            }

            function updateExportUI(processed, total, percent) {
                let p = Math.min(percent, 100);
                $('#export-progress-bar').css('width', p + '%').text(p + '%');
                $('#export-progress-label').text(p + '%');
                $('#export-progress-count').text(
                    Number(processed).toLocaleString('id-ID') + ' / ' +
                    Number(total).toLocaleString('id-ID') + ' data'
                );
            }

            // Fungsi umum: dipakai Excel maupun PDF, beda config saja
            function runExportFlow(config) {
                exportConfig = config;
                resetExportUI();
                $('#modal-export-title').text(config.title);
                $('#btn-download-export').text(config.downloadText);
                $('#modal-export').modal('show');
                $('#export-status-area').html('<div class="text-muted small">Menyiapkan export...</div>');

                // 1. START: minta server buatkan export + jalankan Job
                $.ajax({
                    url: config.startUrl,
                    type: "POST",
                    data: {
                        search: $('#search').val(),
                        category: $('#filter-category').val(),
                        stock_status: $('#filter-stock-status').val()
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.total === 0) {
                            updateExportUI(0, 0, 100);
                            $('#export-status-area').html(
                                '<div class="alert alert-warning">Tidak ada data untuk diexport.</div>'
                                );
                            return;
                        }
                        // 2. POLLING: tanya status tiap 1 detik
                        exportPollTimer = setInterval(function() {
                            pollExportStatus(res.export_id);
                        }, 1000);
                        pollExportStatus(res.export_id);
                    },
                    error: function(xhr) {
                        let msg = 'Gagal memulai export.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        updateExportUI(0, 0, 0);
                        $('#export-status-area').html(
                            '<div class="alert alert-warning">' + msg + '</div>');
                    }
                });
            }

            function pollExportStatus(exportId) {
                $.ajax({
                    url: exportConfig.statusBase + "/" + exportId,
                    type: "GET",
                    success: function(res) {
                        updateExportUI(res.processed, res.total, res.percent);

                        if (res.status === 'done') {
                            clearInterval(exportPollTimer);
                            $('#export-progress-bar').removeClass('bg-primary')
                                .addClass('bg-success');
                            $('#export-status-area').html(
                                '<div class="alert alert-success">' + exportConfig.doneText +
                                '</div>');
                            $('#btn-download-export')
                                .removeClass('d-none')
                                .attr('href', exportConfig.downloadBase + "/" + exportId);
                        } else if (res.status === 'failed') {
                            clearInterval(exportPollTimer);
                            $('#export-progress-bar').removeClass('bg-primary')
                                .addClass('bg-danger');
                            $('#export-status-area').html(
                                '<div class="alert alert-danger">Export gagal: ' +
                                (res.error || 'terjadi kesalahan') + '</div>');
                        }
                    },
                    error: function() {
                        clearInterval(exportPollTimer);
                        $('#export-status-area').html(
                            '<div class="alert alert-danger">Gagal cek status export.</div>');
                    }
                });
            }

            $('#btn-export-excel').on('click', function() {
                runExportFlow({
                    title: 'Export Data Obat',
                    startUrl: "{{ route('obat.export.start') }}",
                    statusBase: "/obat/export-excel/status",
                    downloadBase: "/obat/export-excel/download",
                    downloadText: "Download Excel",
                    doneText: "Export selesai • Download Excel"
                });
            });

            $('#btn-export-pdf').on('click', function() {
                runExportFlow({
                    title: 'Export Laporan Stok Obat (PDF)',
                    startUrl: "{{ route('obat.export.pdf.start') }}",
                    statusBase: "/obat/export-pdf/status",
                    downloadBase: "/obat/export-pdf/download",
                    downloadText: "Download PDF",
                    doneText: "Export selesai • Download PDF"
                });
            });

            // Kalau modal ditutup saat masih jalan, hentikan polling
            $('#modal-export').on('hidden.bs.modal', function() {
                clearInterval(exportPollTimer);
            });
        });
    </script>
@endsection
