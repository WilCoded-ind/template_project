<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Users Management') }}
            </h2>
            @if(auth()->user()->hasPermission('user.create'))
            <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New User
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full  mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
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
                    <!-- Filter Section -->
                    {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <!-- Role Filter -->
                        <div>
                            <label for="roleFilter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Role</label>
                            <select id="roleFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                            <select id="statusFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Bulk Actions -->
                        <div>
                            <label for="bulkAction" class="block text-sm font-medium text-gray-700 mb-1">Bulk Actions</label>
                            <select id="bulkAction" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Action</option>
                                <option value="activate">Activate</option>
                                <option value="deactivate">Deactivate</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-end gap-2">
                            <button id="applyBulkAction" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                Apply
                            </button>
                            <button id="exportBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Export CSV
                            </button>
                        </div>
                    </div> --}}

                    <!-- DataTable -->
                    <div class="overflow-x-auto">
                        <table id="usersTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-1 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-1 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                    <th class="px-1 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                    <th class="px-1 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-1 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                    <th class="px-1 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'username', name: 'username' },
                    { data: 'roles', name: 'roles', orderable: false, searchable: false },
                    { data: 'status', name: 'is_active' },
                    { data: 'created_date', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[5, 'desc']], // Sort by created_at descending
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
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