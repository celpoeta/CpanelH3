@can('download-submitted-form')
    <a href="{{ route('download.form.values.pdf', $formValue->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
        data-bs-original-title="{{ __('Download') }}" class="btn btn-success btn-sm"
        data-toggle="tooltip"><i class="ti ti-file-download"></i></a>
@endcan
@can('show-submitted-form')
    <a href="{{ route('formvalues.show', $formValue->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
        data-bs-original-title="{{ __('Show') }}" title="{{ __('View Survey') }}"
        class="btn btn-info btn-sm" data-toggle="tooltip"><i class="ti ti-eye"></i></a>
@endcan
@can('edit-submitted-form')
    <a href="{{ route('formvalues.edit', $formValue->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
         data-bs-original-title="{{ __('Edit') }}" title="{{ __('Edit Survey') }}"
        class="btn btn-primary btn-sm" data-toggle="tooltip"><i class="ti ti-edit"></i> </a>
@endcan
@can('delete-submitted-form')
    {!! Form::open([
        'method' => 'DELETE',
        'route' => ['formvalues.destroy', $formValue->id],
        'id' => 'delete-form-' . $formValue->id,
        'class' => 'd-inline',
    ]) !!}
    <a href="#" class="btn btn-danger btn-sm show_confirm" data-bs-toggle="tooltip" data-bs-placement="bottom"
        data-bs-original-title="{{ __('Delete') }}" id="delete-form-{{ $formValue->id }}"><i class="ti ti-trash"></i></a>
    {!! Form::close() !!}
@endcan

