@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5 pl-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Edit Role</span></h1>
                </div>
            </div>
        </div>

        <div class="row m-0">
            <div class="col-8">

                <form id="editRole">
                    @method('PUT')
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label>Name<span class="text-danger">*</span></label>
                                <input type="text" {{ $role->id == 1 ? 'readonly' : ''}} name="name" class="form-control @error('name') is-invalid @enderror" required placeholder="Name" value="{{ old('name') ?? $role->name }}">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Description">{{ old('description') ?? $role->description }}</textarea>
                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="panel-group">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" href="#collapse1">Permissions <span class="h6">{{ $role->id == 1 ? '(are non-editable for administrator)': '' }}</span></a>
                                        </h4>
                                    </div>
                                    <div id="collapse1" class="panel-collapse collapse">
                                        <ul class="list-group">
                                            @foreach($modules as $key => $module)
                                                <div class="row">
                                                    <div class="col">
                                                        <li class="list-group-item text-capitalize">{{ $module->name }}

                                                            @if($module->children)
                                                                <ul class="list-group">
                                                                    @foreach($module->children as $child)
                                                                        <div class="row">
                                                                            <div class="col">
                                                                                <li class="list-group-item text-capitalize">{{ $child->name }}</li>
                                                                            </div>

                                                                            @foreach($child->permissions as $key2 => $permit)
{{--                                                                                @if($loop->last)--}}
                                                                                <div class="col-md-9 mt-2 row">
                                                                                    <div class="col custom-control custom-switch">
                                                                                        <input type="hidden" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][create]' }}" value={{ $role->id == 1 ? 1 : 0 }}>
                                                                                        <input type="checkbox" {{ $role->id == 1 ? 'disabled' : '' }} class="custom-control-input" {{ $permit->create ? 'checked': '' }} id="{{ $child->name.'Create' }}" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][create]' }}" value="{{ old('permissions['.$module->name.'][child]['.$child->name.'][create]') ?? 1 }}">
                                                                                        <label class="custom-control-label" for="{{ $child->name.'Create' }}">Create</label>
                                                                                    </div>
                                                                                    <div class="col custom-control custom-switch">
                                                                                        <input type="hidden" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][view]' }}" value={{ $role->id == 1 ? 1 : 0 }}>
                                                                                        <input type="checkbox" {{ $role->id == 1 ? 'disabled' : '' }} class="custom-control-input" {{ $permit->read ? 'checked': '' }} id="{{ $child->name.'View' }}" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][view]' }}" value="{{ old('permissions['.$module->name.'][child]['.$child->name.'][view]') ?? 1 }}">
                                                                                        <label class="custom-control-label" for="{{ $child->name.'View' }}">View</label>
                                                                                    </div>
                                                                                    <div class="col custom-control custom-switch">
                                                                                        <input type="hidden" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][edit]' }}" value={{ $role->id == 1 ? 1 : 0 }}>
                                                                                        <input type="checkbox" {{ $role->id == 1 ? 'disabled' : '' }} class="custom-control-input" {{ $permit->update ? 'checked': '' }} id="{{ $child->name.'Edit' }}" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][edit]' }}" value="{{ old('permissions['.$module->name.'][child]['.$child->name.'][edit]') ?? 1}}">
                                                                                        <label class="custom-control-label" for="{{ $child->name.'Edit' }}">Edit</label>
                                                                                    </div>
                                                                                    <div class="col custom-control custom-switch">
                                                                                        <input type="hidden" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][delete]' }}" value={{ $role->id == 1 ? 1 : 0 }}>
                                                                                        <input type="checkbox" {{ $role->id == 1 ? 'disabled' : '' }} class="custom-control-input" {{ $permit->delete ? 'checked': '' }} id="{{ $child->name.'Delete' }}" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][delete]' }}" value="{{ old('permissions['.$module->name.'][child]['.$child->name.'][delete]') ?? 1}}">
                                                                                        <label class="custom-control-label" for="{{ $child->name.'Delete' }}">Delete</label>
                                                                                    </div>

                                                                                </div>
{{--                                                                                @endif--}}
                                                                            @endforeach
                                                                        </div>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    </div>

                                                    @if(count($module->children) == 0)
                                                    <div class="col-md-9 mt-2 row">

                                                        @foreach($module->permissions as $permission)
                                                            <div class="col custom-control custom-switch">
                                                                <input type="hidden" name="{{ 'permissions['.$module->name.'][create]' }}" value={{ $role->id == 1 ? 1 : 0 }}>
                                                                <input type="checkbox" {{ $role->id == 1 ? 'disabled' : '' }} class="custom-control-input" {{ $permission->create ? 'checked': '' }} id="{{ $module->name.'Create' }}" name="{{ 'permissions['.$module->name.'][create]' }}" value="{{ old('permissions['.$module->name.'][create]') ?? 1 }}">
                                                                <label class="custom-control-label" for="{{ $module->name.'Create' }}">Create</label>
                                                            </div>
                                                            <div class="col custom-control custom-switch">
                                                                <input type="hidden" name="{{ 'permissions['.$module->name.'][view]' }}" value={{ $role->id == 1 ? 1 : 0 }}>
                                                                <input type="checkbox" {{ $role->id == 1 ? 'disabled' : '' }} class="custom-control-input" {{ $permission->read ? 'checked': '' }} id="{{ $module->name.'View' }}" name="{{ 'permissions['.$module->name.'][view]' }}" value="{{ old('permissions['.$module->name.'][view]') ?? 1 }}">
                                                                <label class="custom-control-label" for="{{ $module->name.'View' }}">View</label>
                                                            </div>
                                                            <div class="col custom-control custom-switch">
                                                                <input type="hidden" name="{{ 'permissions['.$module->name.'][edit]' }}" value={{ $role->id == 1 ? 1 : 0 }}>
                                                                <input type="checkbox" {{ $role->id == 1 ? 'disabled' : '' }} class="custom-control-input" {{ $permission->update ? 'checked': '' }} id="{{ $module->name.'Edit' }}" name="{{ 'permissions['.$module->name.'][edit]' }}" value="{{ old('permissions['.$module->name.'][edit]') ?? 1}}">
                                                                <label class="custom-control-label" for="{{ $module->name.'Edit' }}">Edit</label>
                                                            </div>
                                                            <div class="col custom-control custom-switch">
                                                                <input type="hidden" name="{{ 'permissions['.$module->name.'][delete]' }}" value={{ $role->id == 1 ? 1 : 0 }}>
                                                                <input type="checkbox" {{ $role->id == 1 ? 'disabled' : '' }} class="custom-control-input" {{ $permission->delete ? 'checked': '' }} id="{{ $module->name.'Delete' }}" name="{{ 'permissions['.$module->name.'][delete]' }}" value="{{ old('permissions['.$module->name.'][delete]') ?? 1}}">
                                                                <label class="custom-control-label" for="{{ $module->name.'Delete' }}">Delete</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>

                <div class="pt-lg-2">
                    <button form="editRole" formmethod="POST" formaction="{{ route('roles.update', $role->id) }}" type="submit" class="btn btn-primary">{{ 'Update' }}</button>
                    <button for="deleteConfirm" {{ $role->id == 1 ? 'disabled' : ''}} class="btn btn-danger mx-sm-2" data-toggle="modal" {{ $deletePermission ? '': 'disabled' }} data-target="#deleteConfirm">{{ 'Delete' }}</button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                {{ "Please confirm to delete this role" }}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                                <form action="{{ $deletePermission ? route('roles.destroy', $role->id) : '#' }}" method="POST" style="display:inline">
                                    @method('DELETE')
                                    {{ csrf_field() }}
                                    <input type="submit" class="btn btn-danger btn-ok" value="{{ 'Confirm' }}" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

