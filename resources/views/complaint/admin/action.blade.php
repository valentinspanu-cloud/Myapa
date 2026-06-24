@if($complaint['status_id'] != 3)
    <a href="{{ route('complaints.edit', $complaint['id']) }}" title="@lang('labels.edit')"
       class="hover-to-accent">
        <i class="fa fa-edit" aria-hidden="true"></i>
    </a>
@else
    <a href="{{ route('complaints.edit', $complaint['id']) }}" title="@lang('labels.view')"
       class="hover-to-accent">
        <i class="fa fa-eye" aria-hidden="true"></i>
    </a>
@endif
