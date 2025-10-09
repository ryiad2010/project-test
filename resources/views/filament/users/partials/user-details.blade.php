<div class="space-y-2">
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Is Admin:</strong> {{ $user->is_admin ? 'Yes' : 'No' }}</p>
    <p><strong>Created At:</strong> {{ $user->created_at->format('Y-m-d H:i') }}</p>
</div>
