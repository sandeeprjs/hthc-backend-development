@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5 pl-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Add Role</span></h1>
                </div>
            </div>
        </div>

        <div class="row m-0">
            <div class="col-8">

                <form method="POST" action="{{ route('roles.store') }}">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label>Name<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required placeholder="Name" value="{{ old('name') }}">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Description">{{ old('description') }}</textarea>
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
                                            <a data-toggle="collapse" href="#collapse1">Permissions</a>
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
                                                                            <div class="col-md-9 mt-2 row">
                                                                                <div class="col custom-control custom-switch">
                                                                                    <input type="hidden" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][create]' }}" value=0>
                                                                                    <input type="checkbox" class="custom-control-input" id="{{ $child->name.'Create' }}" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][create]' }}" value="{{ old('permissions['.$module->name.'][child]['.$child->name.'][create]') ?? 1 }}">
                                                                                    <label class="custom-control-label" for="{{ $child->name.'Create' }}">Create</label>
                                                                                </div>
                                                                                <div class="col custom-control custom-switch">
                                                                                    <input type="hidden" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][view]' }}" value=0>
                                                                                    <input type="checkbox" class="custom-control-input" id="{{ $child->name.'View' }}" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][view]' }}" value="{{ old('permissions['.$module->name.'][child]['.$child->name.'][view]') ?? 1 }}">
                                                                                    <label class="custom-control-label" for="{{ $child->name.'View' }}">View</label>
                                                                                </div>
                                                                                <div class="col custom-control custom-switch">
                                                                                    <input type="hidden" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][edit]' }}" value=0>
                                                                                    <input type="checkbox" class="custom-control-input" id="{{ $child->name.'Edit' }}" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][edit]' }}" value="{{ old('permissions['.$module->name.'][child]['.$child->name.'][edit]') ?? 1}}">
                                                                                    <label class="custom-control-label" for="{{ $child->name.'Edit' }}">Edit</label>
                                                                                </div>
                                                                                <div class="col custom-control custom-switch">
                                                                                    <input type="hidden" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][delete]' }}" value=0>
                                                                                    <input type="checkbox" class="custom-control-input" id="{{ $child->name.'Delete' }}" name="{{ 'permissions['.$module->name.'][child]['.$child->name.'][delete]' }}" value="{{ old('permissions['.$module->name.'][child]['.$child->name.'][delete]') ?? 1}}">
                                                                                    <label class="custom-control-label" for="{{ $child->name.'Delete' }}">Delete</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    </div>
                                                    @if(count($module->children) == 0)
                                                        <div class="col-md-9 mt-2 row">
                                                            <div class="col custom-control custom-switch">
                                                                <input type="hidden" name="{{ 'permissions['.$module->name.'][create]' }}" value=0>
                                                                <input type="checkbox" class="custom-control-input" id="{{ $module->name.'Create' }}" name="{{ 'permissions['.$module->name.'][create]' }}" value="{{ old('permissions['.$module->name.'][create]') ?? 1 }}">
                                                                <label class="custom-control-label" for="{{ $module->name.'Create' }}">Create</label>
                                                            </div>
                                                            <div class="col custom-control custom-switch">
                                                                <input type="hidden" name="{{ 'permissions['.$module->name.'][view]' }}" value=0>
                                                                <input type="checkbox" class="custom-control-input" id="{{ $module->name.'View' }}" name="{{ 'permissions['.$module->name.'][view]' }}" value="{{ old('permissions['.$module->name.'][view]') ?? 1 }}">
                                                                <label class="custom-control-label" for="{{ $module->name.'View' }}">View</label>
                                                            </div>
                                                            <div class="col custom-control custom-switch">
                                                                <input type="hidden" name="{{ 'permissions['.$module->name.'][edit]' }}" value=0>
                                                                <input type="checkbox" class="custom-control-input" id="{{ $module->name.'Edit' }}" name="{{ 'permissions['.$module->name.'][edit]' }}" value="{{ old('permissions['.$module->name.'][edit]') ?? 1}}">
                                                                <label class="custom-control-label" for="{{ $module->name.'Edit' }}">Edit</label>
                                                            </div>
                                                            <div class="col custom-control custom-switch">
                                                                <input type="hidden" name="{{ 'permissions['.$module->name.'][delete]' }}" value=0>
                                                                <input type="checkbox" class="custom-control-input" id="{{ $module->name.'Delete' }}" name="{{ 'permissions['.$module->name.'][delete]' }}" value="{{ old('permissions['.$module->name.'][delete]') ?? 1}}">
                                                                <label class="custom-control-label" for="{{ $module->name.'Delete' }}">Delete</label>
                                                            </div>
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
                    <button type="submit" class="btn btn-primary mt-2">{{'Submit'}}</button>
                </form>

            </div>
        </div>
    </div>
@endsection
