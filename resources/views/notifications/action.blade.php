<a href="{{ route('notification.view', $notification['id']) }}" title="@lang('labels.view')"
   class="hover-to-accent mr-2">
    <i class="fa fa-eye" aria-hidden="true"></i>
</a>
@if(isAdmin())
<form method="POST" action="{{ route('notification.delete', $notification['id']) }}" style="display:inline"
      onsubmit="return confirm('Ești sigur că vrei să ștergi această notificare?')">
    @csrf
    @method('DELETE')
    <button type="submit" title="Șterge" class="hover-to-accent" style="background:none;border:none;cursor:pointer;padding:0;color:#dc2626;">
        <i class="fa fa-trash" aria-hidden="true"></i>
    </button>
</form>
@endif
