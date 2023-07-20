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
                                    <th>@lang('S.N')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subcategories as $subcategory)
                                    <tr>
                                        <td>{{ $subcategories->firstItem() + $loop->index }}</td>
                                        <td>{{ __($subcategory->name) }}</td>
                                        <td>{{ __($subcategory->category->name) }}</td>
                                        <td>
                                            @php
                                                echo $subcategory->statusBadge;
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary editBtn" data-name="{{ $subcategory->name }}" data-id="{{ $subcategory->id }}" data-category_id="{{ $subcategory->category_id }}" data-status="{{ $subcategory->status }}"><i class="la la-pencil"></i>@lang('Edit')</button>
                                                @if ($subcategory->status == Status::ENABLE)
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to remove this advertise?')" data-action="{{ route('admin.subcategory.status', $subcategory->id) }}"><i class="la la-eye-slash"></i>@lang('Disable')</button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to remove this advertise?')" data-action="{{ route('admin.subcategory.status', $subcategory->id) }}"><i class="la la-eye"></i>@lang('Enable')</button>
                                                @endif
                                            </div>
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
                @if ($subcategories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($subcategories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="subCategoryModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Category')</label>
                            <select class="form-control" name="category_id">
                                <option value="">-- @lang('Select One') --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" type="text" placeholder="@lang('Sub Category Name')" required>
                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addBtn"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"
            var modal = $('#subCategoryModal');

            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add Subcategory')`);
                modal.find('form').attr('action', `{{ route('admin.subcategory.store') }}`);
                modal.find('.statusGroup').hide();
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                var data = $(this).data();
                modal.find('.modal-title').text(`@lang('Update Subcategory')`);
                modal.find('input[name=name]').val(data.name);
                modal.find('form').attr('action', `{{ route('admin.subcategory.store', '') }}/${data.id}`);
                modal.find('select[name=category_id]').val(`${data.category_id}`);
                modal.find('.statusGroup').show();

                if (data.status == 1) {
                    modal.find('input[name=status]').bootstrapToggle('on');
                } else {
                    modal.find('input[name=status]').bootstrapToggle('off');
                }

                modal.modal('show');
            });
            $('#subCategoryModal').on('hidden.bs.modal', function() {
                $('#subCategoryModal form')[0].reset();
            });
        })(jQuery);
    </script>
@endpush
