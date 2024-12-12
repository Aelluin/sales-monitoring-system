<tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                @php
                    $role = $user->roles->first();  // Get the first role assigned to the user
                @endphp

                {{ $role ? $role->name : 'No Role Assigned' }}
            </td>
            <td>
                <!-- Role Assignment Form -->
                <form action="{{ route('admin.users.assignRole', $user->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <select name="role_id" class="form-control">
                            <option value="">Select Role</option>
                            @foreach($roles as $roleOption)
                                <option value="{{ $roleOption->id }}"
                                    {{ isset($role) && $roleOption->id === $role->id ? 'selected' : '' }}>
                                    {{ $roleOption->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Assign/Update Role</button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>
