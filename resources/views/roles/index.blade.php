<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Manajemen Role') }}
            </h2>
            @if(auth()->user()->hasPermission('role.create'))
            <a href="{{ route('roles.create') }}" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                Tambah Role Baru
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Wadah alert untuk notifikasi AJAX -->
            <div id="alertContainer"></div>

            @if(session('success'))
                <div class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Tabel DataTable -->
                    <div>
                        <table id="rolesTable" class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-1 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nama</th>
                                    <th class="px-1 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nama Tampilan</th>
                                    <th class="px-1 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Jumlah Users</th>
                                    <th class="px-1 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Jumlah Permissions</th>
                                    <th class="px-1 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Aksi</th>
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
            table = $('#rolesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('roles.index') }}",
                    data: function(d) {
                    }
                },
                // Konfigurasi kolom-kolom tabel
                columns: [
                    { data: 'name', name: 'name', className: 'text-left' },
                    { data: 'display_name', name: 'display_name', className: 'text-left' },
                    { data: 'users_count', name: 'users_count', className: 'text-left' },
                    { data: 'permissions_count', name: 'permissions_count', className: 'text-left' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-left' }
                ],
                // Pengaturan sorting, pagination, dan responsif
                order: [[0, 'asc']], // Sortir berdasarkan nama secara ascending
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                responsive: true,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ roles",
                    infoEmpty: "Showing 0 to 0 of 0 roles",
                    infoFiltered: "(filtered from _MAX_ total roles)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

        });

        // Delete Role Function
        function deleteRole(roleId) {
            if (!confirm('Are you sure you want to delete this role?')) {
                return;
            }

            $.ajax({
                url: `/roles/${roleId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlert(response.message, 'success');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    let message = xhr.responseJSON?.message || 'An error occurred';
                    showAlert(message, 'error');
                }
            });
        }

        // Show Alert Function
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

            // Auto remove after 5 seconds
            setTimeout(function() {
                $('#alertContainer').html('');
            }, 5000);
        }
    </script>
    @endpush
</x-app-layout>
