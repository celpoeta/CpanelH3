<span>
    @can('edit-user')
        <a class="btn btn-primary btn-sm" href="javascript:void(0);" id="edit-user"
            data-url="{{ route('users.edit', $user->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
            data-bs-original-title="{{ __('Edit') }}"><i class="ti ti-edit"></i></a>
    @endcan
    @can('delete-user')
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['users.destroy', $user->id],
            'id' => 'delete-form-' . $user->id,
            'class' => 'd-inline',
        ]) !!}
        <a href="#" class="btn btn-danger btn-sm show_confirm" id="delete-form-{{ $user->id }}"
            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i
                class="ti ti-trash"></i></a>
        {!! Form::close() !!}
    @endcan
</span>
