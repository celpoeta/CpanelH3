{!! Form::open([
    'route' => 'zoos.store',
    'method' => 'Post',
    'data-validate',
    'enctype' => 'multipart/form-data',
]) !!}
<div class="modal-body">
    <div class="row">

        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('name', 'Nombre científico', ['class' => 'col-form-label']) }}
                {!! Form::text('common_name', null, ['class' => 'form-control', 'required', 'placeholder' => __('Enter name')]) !!}
            </div>
           <div class="form-group  ">
                {{ Form::label('name', 'Nombre científico', ['class' => 'col-form-label']) }}
                {!! Form::text('scientific_name', null, ['class' => 'form-control', 'required', 'placeholder' => __('Enter name')]) !!}
            </div>
            <div class="form-group  ">
                {{ Form::label('name', 'Descripción', ['class' => 'col-form-label']) }}
                {!! Form::text('description', null, ['class' => 'form-control', 'required', 'placeholder' => __('Enter name')]) !!}
            </div>
            <div class="form-group  ">
                {{ Form::label('name', 'En extinción', ['class' => 'col-form-label']) }}
                {!! Form::text('risk', null, ['class' => 'form-control', 'required', 'placeholder' => __('Enter name')]) !!}
            </div>
            <div class="form-group  ">
                {{ Form::label('name', 'nota', ['class' => 'col-form-label']) }}
                {!! Form::text('risk_description', null, ['class' => 'form-control', 'required', 'placeholder' => __('Enter name')]) !!}
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group  ">
                {{ Form::label('name', __('image'), ['class' => 'col-form-label']) }}
                {!! Form::text('url_image', null, ['class' => 'form-control', 'required', 'placeholder' => __('Enter name')]) !!}
            </div>
            <div class="form-group  ">
                {{ Form::label('category_id', __('category'), ['class' => 'col-form-label']) }}
                {!! Form::select('category_id', $categories, null, ['class' => 'form-select', 'id' => 'category_id']) !!}
            </div>
            <div class="form-group  ">
                {{ Form::label('name','Habitat', ['class' => 'col-form-label']) }}
                {!! Form::text('habitat', null, ['class' => 'form-control', 'required', 'placeholder' => __('Enter name')]) !!}
            </div>
            <div class="form-group  ">
                {{ Form::label('name', 'Destribucion geográfica', ['class' => 'col-form-label']) }}
                {!! Form::text('distribution', null, ['class' => 'form-control', 'required', 'placeholder' => __('Enter name')]) !!}
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <div class="float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
    </div>
</div>
{!! Form::close() !!}
