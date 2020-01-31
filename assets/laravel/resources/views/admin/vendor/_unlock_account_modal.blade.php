<div class="modal fade" tabindex="-1" role="dialog" id="unlock-account-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                {{ Form::open([ 'route' => 'admin.auth.login', 'class' => 'automatic-post' ]) }}
                <div class="thumb thumb-rounded text-center">
                    <img src="{!! auth()->user()->image_url !!}" alt="{{ auth()->user()->name }}" style="width: 150px">
                </div>

                <div class="panel-body">
                    <h6 class="content-group text-center text-semibold no-margin-top">
                        {{ auth()->user()->name }}
                        <small class="display-block">
                            {{ trans('auth.admin_lock.help_text') }}
                        </small>
                    </h6>

                    {{ Form::hidden('username', auth()->user()->username) }}

                    <div class="form-group has-feedback">
                        {{ Form::password('password', [
                            'class' => 'form-control',
                            'placeholder' => trans('auth.admin_lock.password_placeholder')
                        ]) }}
                        <div class="form-control-feedback">
                            <i class="fa fa-lock text-muted"></i>
                        </div>
                    </div>

                    <div class="form-group login-options">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="checkbox-inline">
                                    <div class="checker">
                                        <span class="checked">
                                            <input type="checkbox" class="styled" checked="" name="remember">
                                        </span>
                                    </div>
                                    {{ trans('auth.login_form.remember_label') }}
                                </label>
                            </div>

                            <div class="col-sm-6 text-right">
                                <a target="_blank" href="{{ route('admin.password.forgot') }}">
                                    {{ trans('auth.login_form.forgotten_password') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        {{ trans('auth.admin_lock.btn_unlock') }}
                        <i class="fa fa-arrow-right position-right"></i>
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
