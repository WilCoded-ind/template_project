<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Permission') }}
            </h2>
            @if(auth()->user()->hasPermission('permission.create'))
            <a href="{{ route('permissions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tambah Permission Baru
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Wadah alert untuk notifikasi AJAX -->
            <div id="alertContainer"></div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Tabel DataTable -->
                    <div>
                        <table id="permissionsTable" class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-1 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-1 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Tampilan</th>
                                    <th class="px-1 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-1 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <!-- CSS DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    @endpush

    @push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- JS DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        let table;

        $(document).ready(function() {
            // Inisialisasi DataTable dengan konfigurasi server-side
            table = $('#permissionsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('permissions.index') }}",
                    data: function(d) {
                    }
                },
                // Konfigurasi kolom-kolom tabel
                columns: [
                    { data: 'name', name: 'name', className: 'text-left' },
                    { data: 'display_name', name: 'display_name', className: 'text-left' },
                    { data: 'description', name: 'description', className: 'text-left' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-left' }
                ],
                // Pengaturan sorting, pagination, dan responsif
                order: [[0, 'asc']], // Sortir berdasarkan nama secara ascending
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ permission",
                    infoEmpty: "Menampilkan 0 hingga 0 dari 0 permission",
                    infoFiltered: "(difilter dari _MAX_ total permission)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

        });

        // Fungsi untuk menghapus permission
        function deletePermission(permissionId) {
            if (!confirm('Apakah Anda yakin ingin menghapus permission ini?')) {
                return;
            }

            $.ajax({
                url: `/permissions/${permissionId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlert(response.message, 'success');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    let message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                    showAlert(message, 'error');
                }
            });
        }

        // Fungsi untuk menampilkan alert notifikasi
        function showAlert(message, type) {
            let alertClass = type === 'success'
                ? 'bg-green-100 border-green-400 text-green-700'
                : 'bg-red-100 border-red-400 text-red-700';

            let alertHtml = `
                <div class="${alertClass} border px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">${message}</span>
                    <button onclick="this.parentElement.remove()" class="absolute top-0 right-0 px-4 py-3">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>
            `;

            $('#alertContainer').html(alertHtml);

            // Hilangkan alert otomatis setelah 5 detik
            setTimeout(function() {
                $('#alertContainer').html('');
            }, 5000);
        }
    </script>
    @endpush
</x-app-layout>
