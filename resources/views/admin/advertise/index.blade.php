@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Image Type')</th>
                                    <th>@lang('Device')</th>
                                    <th>@lang('Show')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ads as $ad)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb"><img class="plugin_bg" src="{{ getImage(getFilePath('ads') . '/' . @$ad->content->image) }}" alt="image"></div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($ad->type == 1)
                                                <span>@lang('Portrait')</span>
                                            @else
                                                <span>@lang('Landscape')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ad->device == 1)
                                                <span>@lang('Web')</span>
                                            @else
                                                <span>@lang('App')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ad->ads_show == 1)
                                                <span>@lang('Popup')</span>
                                            @else
                                                <span>@lang('Section')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--primary editBtn" data-id="{{ $ad->id }}" data-type="{{ $ad->type }}" data-device="{{ $ad->device }}" data-ads_show="{{ $ad->ads_show }}" data-ads_type="{{ $ad->ads_type }}" @if (@$ad->content->link) data-link="{{ $ad->content->link }}" @endif @if (@$ad->content->image) data-image="{{ asset(getFilePath('ads') . '/' . $ad->content->image) }}" @endif @if (@$ad->content->script) data-script="{{ $ad->content->script }}" @endif><i class="la la-pencil"></i>@lang('Edit')</button>

                                            <button class="btn btn-sm btn-outline--danger confirmationBtn" data-id="{{ $ad->id }}" data-question="@lang('Are you sure to remove this advertise?')" data-action="{{ route('admin.advertise.remove', $ad->id) }}"><i class="la la-trash"></i>@lang('Delete')</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($ads->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($ads) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="advertiseModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Add Advertise')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Image Type')</label>
                            <select class="form-control" name="type" required>
                                <option value="">-- @lang('Select One') --</option>
                                <option value="1">@lang('Portrait')</option>
                                <option value="2">@lang('Landscape')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Device')</label>
                            <select class="form-control" name="device" required>
                                <option value="">-- @lang('Select One') --</option>
                                <option value="1">@lang('Web')</option>
                                <option value="2">@lang('App')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Ads Show')</label>
                            <select class="form-control" name="ads_show" required>
                                <option value="">-- @lang('Select One') --</option>
                                <option value="1">@lang('Pop Up Window')</option>
                                <option value="2">@lang('Section')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Type')</label>
                            <select class="form-control" id="ads_type" name="ads_type" required>
                                <option value="banner">@lang('Banner')</option>
                                <option value="script">@lang('Script')</option>
                            </select>
                        </div>
                        <div class="form-group link d-none">
                            <label>@lang('Link')</label>
                            <input class="form-control" name="link" type="text" placeholder="@lang('Link')">
                        </div>
                        <div class="form-group image d-none">
                            <label>@lang('Image')<span class="text--danger">*</span></label>
                            <div class="image-upload">
                                <div class="thumb">
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview" style="background-image: url({{ getImage('/') }})">
                                            <button class="remove-image" type="button"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input class="profilePicUpload" id="profilePicUpload1" name="image" type="file" accept=".png, .jpg, .jpeg">
                                        <label class="bg--primary" for="profilePicUpload1">@lang('Upload Image')</label>
                                        <small class="text-facebook mt-2">@lang('Supported files'):
                                            <b>@lang('jpeg'), @lang('jpg'), @lang('png').</b>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group script d-none">
                            <label>@lang('Script')</label>
                            <textarea class="form-control" name="script" rows="6" placeholder="@lang('Write Your Script')"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection
@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addBtn"><i class="la la-plus"></i> @lang('Add Advertise')</button>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            var modal = $('#advertiseModal');
            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add Advertise')`);
                modal.find('form').attr('action', `{{ route('admin.advertise.store') }}`);
                adsType('banner')
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                var data = $(this).data();
                modal.find('.modal-title').text(`@lang('Update Advertise')`);
                modal.find('form').attr('action', `{{ route('admin.advertise.store', '') }}/${data.id}`);
                modal.find('select[name=type]').val(data.type);
                modal.find('select[name=device]').val(data.device);
                modal.find('select[name=ads_show]').val(data.ads_show);
                modal.find('select[name=ads_type]').val(data.ads_type);
                adsType(data.ads_type);
                modal.find('[name=link]').val(data.link);
                modal.find('[name=script]').val(data.script);
                modal.find('.profilePicPreview').attr('style', `background-image: url(${data.image})`);
                modal.modal('show');
            });


            $('#ads_type').on('change', function() {
                var type = $(this).val();
                adsType(type);
            }).change();

            function adsType(value) {
                if (value == 'script') {
                    $('.link').addClass('d-none');
                    $('.image').addClass('d-none');
                    $('.script').removeClass('d-none');
                } else {
                    $('.link').removeClass('d-none');
                    $('.image').removeClass('d-none');
                    $('.script').addClass('d-none');
                }
            }

            var defautlImage = `{{ getImage(getFilePath('ads')) }}`;
            modal.on('hidden.bs.modal', function() {
                modal.find('.profilePicPreview').attr('style', `background-image: url(${defautlImage})`);
                $('#advertiseModal form')[0].reset();
            });

        })(jQuery);
    </script>
@endpush
