<x-adminlte-layout>
    <div>
        <h1 class="my-1 text-3xl font-semibold text-gray-900">{{ __('Manajemen Menu') }}</h1>
    </div>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            <div id="alertContainer"></div>

            @if (session('success'))
                <div class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="mb-3 d-flex justify-content-start">
                @if (auth()->user()->hasPermission('menu.create'))
                    <a href="{{ route('menus.create') }}" class="btn btn-secondary">Tambah Menu Baru</a>
                @endif
            </div>

            <div class="overflow-hidden bg-white shadow-md sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- DataTable -->
                    <div>
                        <table id="menusTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Menu</th>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Icon</th>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Route/URL</th>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Permission</th>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Urutan</th>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Status</th>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Aksi</th>
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
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    @endpush

    @push('scripts')
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

        <script>
            let table;

            $(document).ready(function() {
                // Initialize DataTable
                table = $('#menusTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('menus.index') }}",
                        data: function(d) {}
                    },
                    columns: [{
                            data: 'display_name',
                            name: 'display_name'
                        },
                        {
                            data: 'icon',
                            name: 'icon'
                        },
                        {
                            data: 'route_url',
                            name: 'route_url'
                        },
                        {
                            data: 'permission_name',
                            name: 'permission_name'
                        },
                        {
                            data: 'order',
                            name: 'order'
                        },
                        {
                            data: 'is_active',
                            name: 'is_active'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [4, 'asc']
                    ], // Sort by order ascending
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    responsive: true,
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ menus",
                        infoEmpty: "Showing 0 to 0 of 0 menus",
                        infoFiltered: "(filtered from _MAX_ total menus)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });
            });

            // Delete Menu Function
            function deleteMenu(menuId) {
                if (!confirm('Are you sure you want to delete this menu?')) {
                    return;
                }

                $.ajax({
                    url: `/menus/${menuId}`,
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
                let alertClass = type === 'success' ?
                    'bg-green-100 border-green-400 text-green-700' :
                    'bg-red-100 border-red-400 text-red-700';

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
</x-adminlte-layout>
