<!-- resources/views/users/partials/actions.blade.php -->
<div>
    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm">{{__('translate.Edit')}}</a>
    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">{{__('translate.Delete')}}</button>
    </form>
</div>
