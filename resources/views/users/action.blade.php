<a href="{{ route('users.edit', $user->id) }}"><i title="Editeaza" class="fa fa-edit hover-to-accent"></i></a>
<form action="{{ route('users.delete', $user->id) }}" class="d-inline-block user-{{$user->id}}" method="POST" name="delete-{{$user->id}}">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="DELETE"/>
    <button class="delete-icon-btn btn-icon-empty delete-user-btn" data-toggle="modal" data-target="#delete-confirm-modal" type="button"
            data-username="{{$user->name}}" data-userid="{{$user->id}}">
        <i title="Sterge" class="fa fa-trash-alt"></i>
    </button>
</form>
