@if ($categories->hasPages())
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@endif

