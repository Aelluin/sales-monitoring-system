<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Create Sales</title>
    <style>
        /* General Styling for Success and Error Messages */
        .success,
        .error {
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 4px;
        }

        .success {
            background-color: #2ecc71;
            color: white;
        }

        .error {
            background-color: #e74c3c;
            color: white;
        }

        /* General Styles */
        body {
            background-color: #ffffff;
            color: #333333;
        }

        header {
            background-color: #f8f8f8;
            color: #333333;
        }

        /* Form Styling */
        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Action Button Styling */
        .create-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .create-button:hover {
            background-color: #219150;
        }

        .back-link {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .back-link:hover {
            background-color: #2980b9;
        }

        /* Enhanced Table Styling */
        .table {
            width: 90%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;

        }

        .table th,
        .table td {
            text-align: left;
            padding: 8px 16px;
            border-bottom: 1px solid #eaeaea;
        }

        .table th {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        .table tbody tr:hover {
            background-color: #f1f5f9;
        }

        .badge-success {
            background-color: #27ae60;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 16px;
        }

        .badge-secondary {
            background-color: #b2bec3;
            color: #2d3436;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 16px;
        }

        .header-text {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <x-app-layout>
        <div x-data="{ sidebarOpen: true, dropdownOpen: false, openCreateUserModal: false }" class="flex h-screen">
            <!-- Sidebar -->
            <div :class="sidebarOpen ? 'w-64' : 'w-20'" class="flex flex-col h-full transition-all duration-300"
                style="background-color: #15151D; color: #ffffff;">
                <div class="flex items-center justify-between p-4 border-b border-blue-700">
                    <div class="flex justify-center w-full">
                        <img x-show="sidebarOpen" src="{{ asset('img/gg.png') }}" alt="My Dashboard"
                            class="h-14 w-50 object-contain mx-auto" />
                    </div>
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white focus:outline-none">
                        <span class="material-icons text-2xl">menu</span>
                    </button>
                </div>

                <nav class="flex-1 mt-4 space-y-2 px-2">
                    <a href="/dashboard"
                        class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                        <span class="material-icons mr-4 text-xl">dashboard</span>
                        <span x-show="sidebarOpen" class="flex-1 text-base">Dashboard</span>
                    </a>
                    <a href="/products"
                        class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                        <span class="material-icons mr-4 text-xl">inventory</span>
                        <span x-show="sidebarOpen" class="flex-1 text-base">Products</span>
                    </a>
                    <a href="/sales"
                        class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                        <span class="material-icons mr-4 text-xl">show_chart</span>
                        <span x-show="sidebarOpen" class="flex-1 text-base">Sales</span>
                    </a>

                    <!-- Collapsible Report Menu -->
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <a @click="dropdownOpen = !dropdownOpen" href="#"
                            class="flex items-center py-3 px-4 rounded-md text-lg text-white hover:bg-blue-700 transition-all duration-200">
                            <span class="material-icons mr-4 text-xl">assessment</span>
                            <span x-show="sidebarOpen" class="flex-1 text-base">Report</span>
                            <span class="material-icons ml-auto">arrow_drop_down</span>
                        </a>
                        <div x-show="dropdownOpen" x-transition @click.outside="dropdownOpen = false"
                            class="pl-12 mt-2 space-y-2">
                            <a href="/sales/daily"
                                class="flex items-center py-2 px-4 text-sm rounded-md text-gray-200 hover:bg-blue-600 transition-all duration-200">
                                <span class="material-icons mr-2">event</span> Daily Sales
                            </a>
                            <a href="/sales/weekly"
                                class="flex items-center py-2 px-4 text-sm rounded-md text-gray-200 hover:bg-blue-600 transition-all duration-200">
                                <span class="material-icons mr-2">calendar_view_week</span> Weekly Sales
                            </a>
                            <a href="/sales/monthly"
                                class="flex items-center py-2 px-4 text-sm rounded-md text-gray-200 hover:bg-blue-600 transition-all duration-200">
                                <span class="material-icons mr-2">date_range</span> Monthly Sales
                            </a>
                        </div>
                    </div>
                    @if(Auth::user() && Auth::user()->hasRole('admin'))
                    <!-- Logs Button -->
                    <a href="/logs" class="flex items-center py-3 px-4 rounded-md text-lg text-white hover:bg-blue-700 transition-all duration-200 logs-button"
                        :class="{'logs-button-open': dropdownOpen}">
                        <span class="material-icons mr-4 text-xl">history</span>
                        <span x-show="sidebarOpen" class="flex-1 text-base">Logs</span>
                    </a>
                    <a href="/admin/users"
                        class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                        <span class="material-icons mr-4 text-xl">people</span>
                        <span x-show="sidebarOpen" class="flex-1 text-base">User Management</span>
                    </a>
                    @endif
                </nav>
            </div>
<!-- Main Content -->
<div class="flex-1 p-6 flex flex-col items-center">
    <h1 class="header-text text-2xl font-bold mb-6 text-center">User Role Management</h1>

    <!-- Display Success/Error Messages -->
    <div class="message-area mb-6 w-full max-w-2xl">
        @if(session('success'))
            <div class="success bg-green-500 text-white p-3 rounded-lg text-center">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="error bg-red-500 text-white p-3 rounded-lg text-center">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Create Gmail/User Button -->
    <div class="mt-6 mb-6 flex justify-center w-full">
        <button @click="openCreateUserModal = true" class="create-button px-6 py-3 text-lg bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
            Create Gmail/User
        </button>
    </div>

    <!-- Modal for Creating Gmail/User -->
    <div x-show="openCreateUserModal" x-transition @click.outside="openCreateUserModal = false" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
        <div class="bg-white p-6 w-96 rounded-lg">
            <h2 class="text-2xl font-semibold mb-4">Create Gmail/User</h2>
            <form action="{{ route('users.create') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium">Name</label>
                    <input type="text" name="name" id="name" class="border border-gray-300 rounded-lg w-full py-2 px-3" placeholder="Enter name" required />
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" id="email" class="border border-gray-300 rounded-lg w-full py-2 px-3" placeholder="Enter email" required />
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium">Password</label>
                    <input type="password" name="password" id="password" class="border border-gray-300 rounded-lg w-full py-2 px-3" placeholder="Enter password" required />
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="border border-gray-300 rounded-lg w-full py-2 px-3" placeholder="Confirm password" required />
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium">Role</label>
                    <select name="role" id="role" class="border border-gray-300 rounded-lg w-full py-2 px-3">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-between mt-4">
                    <button type="button" @click="openCreateUserModal = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-all">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">Create</button>
                </div>
            </form>
        </div>
    </div>

   <!-- User Table -->
<div class="overflow-x-auto mt-6 w-full max-w-8xl">
    <table class="table w-full table-auto border-collapse text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-sm font-medium text-gray-700">Name</th>
                <th class="px-6 py-3 text-sm font-medium text-gray-700">Email</th>
                <th class="px-6 py-3 text-sm font-medium text-gray-700">Role</th>
                <th class="px-6 py-3 text-sm font-medium text-gray-700">Assign Role</th>
                <th class="px-6 py-3 text-sm font-medium text-gray-700">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            @if(!$user->is_archived)  <!-- This ensures you only display non-archived users -->
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-sm">
                        @php
                            $role = $user->roles->first();
                        @endphp
                        <span class="{{ $role ? 'badge-success' : 'badge-secondary' }}">
                            {{ $role ? $role->name : 'No Role Assigned' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.users.assignRole', $user->id) }}" method="POST" class="flex items-center justify-center space-x-3">
                            @csrf
                            <select name="role_id" class="form-select border-2 rounded-lg py-2 px-3 text-gray-700">
                                <option value="">Select Role</option>
                                @foreach($roles as $roleOption)
                                    <option value="{{ $roleOption->id }}" {{ isset($role) && $roleOption->id === $role->id ? 'selected' : '' }}>
                                        {{ $roleOption->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700 transition-all">Assign</button>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.users.archive', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700 transition-all">Archive</button>
                        </form>
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
    <a href="{{ route('users.archived') }}" class="btn btn-primary mt-4">View Archived Users</a>
</div>



        </div>
    </x-app-layout>
</body>

</html>
