@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.item.update', $item->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>@lang('Portrait Image')</label>
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview" style="background-image: url({{ getImage(getFilePath('item_portrait') . '/' . @$item->image->portrait) }})">
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
                                            <div class="profilePicPreview" style="background-image: url({{ getImage(getFilePath('item_landscape') . '/' . @$item->image->landscape) }})">
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
                        <div class="row">
                            <div class="form-group @if ($item->item_type != 1) col-md-12 @else col-md-6 @endif">
                                <label>@lang('Title')</label>
                                <input class="form-control" name="title" type="text" value="{{ $item->title }}" placeholder="Title">
                            </div>
                            @if ($item->item_type == 1)
                                <div class="form-group col-md-6">
                                    <label>@lang('Version')</label>
                                    <select class="form-control" name="version">
                                        <option value="0">@lang('Free')</option>
                                        <option value="1">@lang('Paid')</option>
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Category')</label>
                                <select class="form-control" name="category">
                                    <option value="">-- @lang('Select One') --</option>
                                    @foreach ($categories as $category)
                                        <option data-subcategories="{{ $category->subcategories }}" value="{{ $category->id }}">{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Sub Category')</label>
                                <select class="form-control" name="sub_category_id">
                                    <option value="">-- @lang('Select One') --</option>
                                    @foreach ($subcategories as $sub_categorie)
                                        <option value="{{ $sub_categorie->id }}">{{ __($sub_categorie->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Preview Text')</label>
                                <textarea class="form-control" name="preview_text" rows="5" placeholder="@lang('Preview Text')">{{ $item->preview_text }}</textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Description')</label>
                                <textarea class="form-control" name="description" rows="5" placeholder="@lang('Description')">{{ $item->description }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>@lang('Director')</label>
                                <select class="form-control select2-auto-tokenize director-option" name="director[]" multiple="multiple" required>
                                    @foreach (explode(',', $item->team->director) as $director)
                                        <option value="{{ $director }}" selected>{{ $director }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Producer')</label>
                                <select class="form-control select2-auto-tokenize director-option" name="producer[]" multiple="multiple" required>
                                    @foreach (explode(',', $item->team->producer) as $producer)
                                        <option value="{{ $producer }}" selected>{{ $producer }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Ratings') <small class="text--primary">(@lang('maximum 10 star'))</small></label>
                                <div class="input-group">
                                    <input class="form-control" name="ratings" type="text" value="{{ $item->ratings }}" placeholder="@lang('Ratings')">
                                    <span class="input-group-text"><i class="las la-star"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Genres')</label>
                                <select class="form-control select2-auto-tokenize genres-option" name="genres[]" multiple="multiple" required>
                                    @foreach (explode(',', @$item->team->genres) as $genre)
                                        <option value="{{ $genre }}" selected>{{ $genre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Languages')</label>
                                <select class="form-control select2-auto-tokenize language-option" name="language[]" multiple="multiple" required>
                                    @foreach (explode(',', @$item->team->language) as $lang)
                                        <option value="{{ $lang }}" selected>{{ $lang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-control-label">@lang('Casts')</label>
                                <small class="text-facebook ml-2 mt-2">@lang('Separate multiple by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>

                                <select class="form-control select2-auto-tokenize" name="casts[]" placeholder="Add short words which better describe your site" multiple="multiple" required>
                                    @foreach (explode(',', $item->team->casts) as $cast)
                                        <option value="{{ $cast }}" selected>{{ $cast }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Tags')</label>
                                <small class="text-facebook ml-2 mt-2">@lang('Separate multiple by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>
                                <select class="form-control select2-auto-tokenize" name="tags[]" placeholder="Add short words which better describe your site" multiple="multiple" required>
                                    @foreach (explode(',', $item->tags) as $tag)
                                        <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>@lang('Total Views')</label>
                                <input class="form-control" name="view" type="text" value="{{ @$item->view }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Status')</label>
                                <input name="status" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Active')" data-off="@lang('Inactive')" type="checkbox" @if ($item->status) checked @endif>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Featured')</label>
                                <input name="featured" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Yes')" data-off="@lang('No')" type="checkbox" @if ($item->featured) checked @endif>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Trending')</label>
                                <input name="trending" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Yes')" data-off="@lang('No')" type="checkbox" @if ($item->trending) checked @endif>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Single Section')</label>
                                <input name="single" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Yes')" data-off="@lang('No')" type="checkbox" @if ($item->single) disabled checked @endif>
                            </div>
                            @if ($item->item_type == 1)
                                <div class="col-md-4 form-group">
                                    <label>@lang('Trailer')</label>
                                    <input name="is_trailer" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Yes')" data-off="@lang('No')" type="checkbox" @if ($item->is_trailer) checked @endif>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.item.index') }}"><i class="la la-undo"></i> @lang('Back')</a>
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

            $('select[name=category]').val('{{ $item->category->id }}');
            $('select[name=sub_category_id]').val('{{ @$item->sub_category->id }}');
            $('select[name=version]').val('{{ @$item->version }}');
        })(jQuery);
    </script>
@endpush
