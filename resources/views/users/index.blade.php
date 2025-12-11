<x-adminlte-layout>
    <div>
        <h1 class="my-1 text-3xl font-semibold text-gray-900">{{ __('Users Management') }}</h1>
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
                @if (auth()->user()->hasPermission('user.create'))
                    <a href="{{ route('users.create') }}" class="btn btn-secondary">Add New User</a>
                @endif
            </div>

            <div class="overflow-hidden bg-white shadow-md sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- DataTable -->
                    <div>
                        <table id="usersTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Nama</th>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Username</th>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Roles</th>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Status</th>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Created At</th>
                                    <th
                                        class="px-1 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-center bg-white divide-y divide-gray-200">
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
                table = $('#usersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('users.index') }}",
                        data: function(d) {
                            d.role_filter = $('#roleFilter').val();
                            d.status_filter = $('#statusFilter').val();
                        }
                    },
                    columns: [{
                            data: 'name',
                            name: 'name',
                        },
                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'roles',
                            name: 'roles',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'is_active'
                        },
                        {
                            data: 'created_date',
                            name: 'created_at'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [5, 'desc']
                    ], // Sort by created_at descending
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    responsive: true,
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ users",
                        infoEmpty: "Showing 0 to 0 of 0 users",
                        infoFiltered: "(filtered from _MAX_ total users)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });

                // Filter change events
                $('#roleFilter, #statusFilter').on('change', function() {
                    table.ajax.reload();
                });

                // Select All checkbox
                $('#selectAll').on('click', function() {
                    $('.user-checkbox').prop('checked', this.checked);
                });

                // Individual checkbox
                $(document).on('change', '.user-checkbox', function() {
                    if ($('.user-checkbox:checked').length === $('.user-checkbox').length) {
                        $('#selectAll').prop('checked', true);
                    } else {
                        $('#selectAll').prop('checked', false);
                    }
                });

                // Bulk Action
                $('#applyBulkAction').on('click', function() {
                    let action = $('#bulkAction').val();
                    let selectedIds = [];

                    $('.user-checkbox:checked').each(function() {
                        selectedIds.push($(this).val());
                    });

                    if (!action) {
                        showAlert('Please select an action', 'error');
                        return;
                    }

                    if (selectedIds.length === 0) {
                        showAlert('Please select at least one user', 'error');
                        return;
                    }

                    if (!confirm(`Are you sure you want to ${action} ${selectedIds.length} user(s)?`)) {
                        return;
                    }

                    $.ajax({
                        url: "{{ route('users.bulk-action') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            action: action,
                            user_ids: selectedIds
                        },
                        success: function(response) {
                            showAlert(response.message, 'success');
                            table.ajax.reload();
                            $('#selectAll').prop('checked', false);
                            $('#bulkAction').val('');
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message || 'An error occurred';
                            showAlert(message, 'error');
                        }
                    });
                });

                // Export CSV
                $('#exportBtn').on('click', function() {
                    let url = "{{ route('users.export') }}?";
                    let params = [];

                    let search = table.search();
                    if (search) params.push('search=' + encodeURIComponent(search));

                    let roleFilter = $('#roleFilter').val();
                    if (roleFilter) params.push('role_filter=' + roleFilter);

                    let statusFilter = $('#statusFilter').val();
                    if (statusFilter) params.push('status_filter=' + statusFilter);

                    window.location.href = url + params.join('&');
                });
            });

            // Delete User Function
            function deleteUser(userId) {
                if (!confirm('Are you sure you want to delete this user?')) {
                    return;
                }

                $.ajax({
                    url: `/users/${userId}`,
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
