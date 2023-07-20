@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.item.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="d-flex justify-content-end flex-wrap gap-3">
                            <div class="form-group">
                                <input class="form-control" name="id" type="number" placeholder="@lang('Enter TMDB ID Ex: 1000')">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="item_type">
                                    <option value="1">@lang('Single Item')</option>
                                    <option value="2">@lang('Episode Item')</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-outline--dark fetchBtn h-45" type="button"><i class="las la-server"></i> @lang('Fetch')</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>@lang('Portrait Image')</label>
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview portrait" style="background-image: url({{ getImage('/') }})">
                                                <button class="remove-image" type="button"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input class="profilePicUpload" id="profilePicUpload1" name="portrait" type="file" accept=".png, .jpg, .jpeg">
                                            <label class="bg--success" for="profilePicUpload1">@lang('Upload Portrait Image')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-8">
                                <label>@lang('Landscape Image')</label>
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview landscape" style="background-image: url({{ getImage('/') }})">
                                                <button class="remove-image" type="button"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input class="profilePicUpload" id="profilePicUpload2" name="landscape" type="file" accept=".png, .jpg, .jpeg">
                                            <label class="bg--success" for="profilePicUpload2">@lang('Upload Landscape Image')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input name="portrait_url" type="hidden" value="">
                        <input name="landscape_url" type="hidden" value="">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Title')</label>
                                <input class="form-control" name="title" type="text" value="{{ old('title') }}" placeholder="@lang('Title')">
                            </div>
                            <div class="form-group col-md-6 version">
                                <label>@lang('Version')</label>
                                <select class="form-control" name="version">
                                    <option value="">@lang('Select One')</option>
                                    <option value="0">@lang('Free')</option>
                                    <option value="1">@lang('Paid')</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Category')</label>
                                <select class="form-control" name="category">
                                    <option value="">@lang('Select One')</option>
                                    @foreach ($categories as $category)
                                        <option data-subcategories="{{ $category->subcategories }}" value="{{ $category->id }}">{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Sub Category')</label>
                                <select class="form-control" name="sub_category_id">
                                    <option value="">@lang('Select One')</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Preview Text')</label>
                                <textarea class="form-control" name="preview_text" rows="5" placeholder="@lang('Preview Text')">{{ old('preview_text') }}</textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Description')</label>
                                <textarea class="form-control" name="description" rows="5" placeholder="@lang('Description')">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>@lang('Director')</label>
                                <select class="form-control select2-auto-tokenize director-option" name="director[]" multiple="multiple" required></select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Producer')</label>
                                <select class="form-control select2-auto-tokenize producer-option" name="producer[]" multiple="multiple" required></select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Ratings') <small class="text--primary">(@lang('maximum 10 star'))</small></label>
                                <div class="input-group">
                                    <input class="form-control" name="ratings" type="number" value="{{ old('ratings') }}" step="any" placeholder="Ratings">
                                    <span class="input-group-text"><i class="las la-star"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Genres')</label>
                                <select class="form-control select2-auto-tokenize genres-option" name="genres[]" multiple="multiple" required></select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Languages')</label>
                                <select class="form-control select2-auto-tokenize language-option" name="language[]" multiple="multiple" required></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-control-label">@lang('Casts')</label>
                                <small class="text-facebook ml-2 mt-2">@lang('Separate multiple by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>

                                <select class="form-control select2-auto-tokenize cast-option" name="casts[]" multiple="multiple" required></select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Tags')</label>
                                <small class="text-facebook ml-2 mt-2">@lang('Separate multiple by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>

                                <select class="form-control select2-auto-tokenize tag-option" name="tags[]" multiple="multiple" required>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.item.index') }}" />
@endpush
@push('script')
    <script>
        (function($) {
            "use strict"
            $('[name=category]').change(function() {
                var subcategoryOption = '<option>@lang('Select One')</option>';
                var subcategories = $(this).find(':selected').data('subcategories');

                subcategories.forEach(subcategory => {
                    subcategoryOption += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                });

                $('[name=sub_category_id]').html(subcategoryOption);
            });

            $('select[name=item_type]').change(function() {
                if ($(this).val() == '1') {
                    $('.version').removeClass('d-none');
                } else {
                    $('.version').addClass('d-none');
                }
            });
            $('select[name=version]').val('{{ old('version') }}');
            $('select[name=category]').val('{{ old('category') }}');
            $('select[name=sub_category_id]').val('{{ old('sub_category_id') }}');


            $('.fetchBtn').on('click', function(e) {
                e.preventDefault();
                let data = {};
                data.id = $('[name=id]').val();
                data.item_type = $('[name=item_type]').find(":selected").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{ route('admin.item.fetch') }}",
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            var casts = response.casts;
                            var tags = response.tags;

                            var portraitImage = `https://image.tmdb.org/t/p/original${data.poster_path}`;
                            var landscapeImage = `https://image.tmdb.org/t/p/original${data.backdrop_path}`;

                            $('.portrait').attr('style', `background-image: url(${portraitImage})`);
                            $('.landscape').attr('style', `background-image: url(${landscapeImage})`);
                            $('[name=portrait_url]').val(portraitImage);
                            $('[name=landscape_url]').val(landscapeImage);

                            $('[name=ratings]').val(data.vote_average);
                            $('[name=title]').val(data.title ?? data.name);
                            $('[name=preview_text]').val(data.tagline);
                            $('[name=description]').val(data.overview);

                            // Cast list
                            var castOption = '';
                            $.each(casts.cast, function(index, value) {
                                castOption += `<option value="${value.name}" selected>${value.name}</option>`
                            });
                            $('.cast-option').html(castOption);

                            // producer
                            var producerOption = '';
                            $.each(casts.crew, function(index, value) {
                                if (value.job == "Producer") {
                                    producerOption += `<option value="${value.name}" selected>${value.name}</option>`
                                }
                            });
                            $('.producer-option').html(producerOption);

                            // director
                            var directorOption = '';
                            $.each(casts.crew, function(index, value) {
                                if (value.known_for_department == "Directing" && (value.job == "Screenplay" || value.job == "Director")) {
                                    directorOption += `<option value="${value.name}" selected>${value.name}</option>`
                                }
                            });
                            $('.director-option').html(directorOption);

                            if (directorOption == '') {
                                $.each(data.created_by, function(index, value) {
                                    directorOption += `<option value="${value.name}" selected>${value.name}</option>`
                                });
                                $('.director-option').html(directorOption);
                            }

                            // genres
                            var genresOption = '';
                            $.each(data.genres, function(index, value) {
                                genresOption += `<option value="${value.name}" selected>${value.name}</option>`
                            });
                            $('.genres-option').html(genresOption);
                            // language
                            var langOption = '';
                            $.each(data.spoken_languages, function(index, value) {
                                langOption += `<option value="${value.name}" selected>${value.name}</option>`
                            });
                            $('.language-option').html(langOption);

                            // tags
                            var tagOption = '';
                            $.each(tags.keywords ?? tags.results, function(index, value) {
                                tagOption += `<option value="${value.name}" selected>${value.name}</option>`
                            });
                            $('.tag-option').html(tagOption);

                            notify('success', 'Data imported successfully');

                        } else {
                            notify('error', response.error);
                        }
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
